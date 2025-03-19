/**
 * App Kanban
 */

'use strict';

(async function () {
  let boards;
  const kanbanSidebar = document.querySelector('.kanban-update-item-sidebar'),
    kanbanWrapper = document.querySelector('.kanban-wrapper'),
    commentEditor = document.querySelector('.comment-editor'),
    kanbanAddNewBoard = document.querySelector('.kanban-add-new-board'),
    kanbanAddNewInput = [].slice.call(document.querySelectorAll('.kanban-add-board-input')),
    kanbanAddBoardBtn = document.querySelector('.kanban-add-board-btn'),
    datePicker = document.querySelector('#due-date'),
    select2 = $('.select2'), // ! Using jquery vars due to select2 jQuery dependency
    assetsPath = document.querySelector('html').getAttribute('data-assets-path'),
    updateTaskButton = document.querySelector('#updateTask'),
    deleteTaskButton = document.querySelector('#deleteTask');

  // Init kanban Offcanvas
  const kanbanOffcanvas = new bootstrap.Offcanvas(kanbanSidebar);

  const urlParams = new URLSearchParams(window.location.search);
  const uid = urlParams.get('uid');

  // Get kanban data
  // const kanbanResponse = await fetch(assetsPath + 'json/kanban.json');
    try {
        // Извлекаем ID из URL
        const path = window.location.pathname;
        const match = path.match(/\/dashboard\/project\/(\d+)/);
        const projectId = match ? match[1] : null;

        if (!projectId) {
            console.error('ID проекта не найден в URL');
            return;
        }

        // Формируем URL для запроса
        let apiUrl = `/api/task/project/${projectId}`;

        // Если uid существует, добавляем его как параметр
        if (uid) {
            apiUrl += `?uid=${uid}`;
        }

        // Выполняем GET-запрос
        const kanbanResponse = await fetch(apiUrl);

        if (!kanbanResponse.ok) {
            throw new Error(`Ошибка: ${kanbanResponse.status}`);
        }
        boards = await kanbanResponse.json();
    } catch (error) {
        console.error('Ошибка при загрузке данных:', error);
    }

  // if (!kanbanResponse.ok) {
  //   console.error('error', kanbanResponse);
  // }
  // boards = await kanbanResponse.json();

  // datepicker init
  if (datePicker) {
    datePicker.flatpickr({
      monthSelectorType: 'static',
      altInput: true,
      altFormat: 'j F, Y',
      dateFormat: 'Y-m-d'
    });
  }

  // Показываем задачу, если ее указали в url
    // Функция для получения параметров из URL
    function getUrlParameter(name) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(name);
    }

    // Функция для открытия модального окна с данными задачи
    async function openTaskModal(taskId) {
        try {
            const response = await fetch(`/api/task/${taskId}?uid=${uid}`);
            if (!response.ok) {
                throw new Error('Ошибка при получении данных задачи');
            }
            const task = await response.json();

            // Заполняем модальное окно данными задачи
            const title = task.title || '';
            const date = task.date ? formatDate(task.date) : null;
            const description = task.description || '';
            const label = task.label || '';
            const avatars = task.responsible.map(user => user.first_name).join(', ');
            //const files = task.files.map(file => file.src).join(',');
            const files = task.files
                .filter(file => file.src) // Оставляем только объекты с полем src
                .map(file => file.src)   // Получаем значения src
                .join(',');

            // Открываем модальное окно
            kanbanOffcanvas.show();

            // Заполняем поля формы
            kanbanSidebar.querySelector('#title').value = title;
            kanbanSidebar.querySelector('#due-date').nextSibling.value = date;
            kanbanSidebar.querySelector('#description').value = description;

            document.querySelector('#updateTask').setAttribute('data-id', task.id)
            document.querySelector('#deleteTask').setAttribute('data-id', task.id)

            // Обрабатываем файлы
            const filesDiv = document.querySelector('#files');
            filesDiv.innerHTML = ''; // Очищаем предыдущие файлы

            if (task.files && task.files.length > 0) {
                task.files.forEach(file => {
                    const { src, id } = file; // Извлекаем src и id файла
                    if (src) {
                        // Создаем ссылку на файл или изображение
                        let link;
                        if (/\.(jpg|jpeg|png|gif)$/i.test(src)) {
                            link = createImageLink(src, id);
                        } else {
                            link = createDocumentLink(src, id);
                        }

                        // Добавляем кнопку "X" внутрь ссылки
                        const deleteButton = document.createElement('button');
                        deleteButton.innerHTML = 'X';
                        deleteButton.classList.add('delete-file-button');

                        // Сохраняем ID файла в атрибуте data-id
                        deleteButton.setAttribute('data-id', id);

                        // Предотвращаем переход по ссылке при клике на кнопку
                        deleteButton.addEventListener('click', async (event) => {
                            event.preventDefault(); // Предотвращаем переход по ссылке
                            event.stopPropagation(); // Останавливаем всплытие события

                            const fileId = deleteButton.getAttribute('data-id'); // Получаем ID файла
                            const confirmed = confirm('Вы уверены, что хотите удалить этот файл #'+fileId+'?');

                            if (confirmed) {
                                try {
                                    const response = await fetch(`/api/file/${fileId}?uid=${uid}`, {
                                        method: 'DELETE',
                                    });

                                    if (!response.ok) {
                                        throw new Error('Ошибка при удалении файла');
                                    }
                                    // Удаляем ссылку с кнопкой из DOM
                                    link.remove();
                                } catch (error) {
                                    console.error('Ошибка:', error.message);
                                    alert('Не удалось удалить файл. Попробуйте позже.');
                                }
                            }
                        });

                        // Добавляем кнопку внутрь ссылки
                        link.appendChild(deleteButton);

                        // Добавляем ссылку в DOM
                        filesDiv.appendChild(link);
                    }
                });
            }

            // Обновляем метку
            $('.kanban-update-item-sidebar').find(select2).val(label).trigger('change');
            // Обновляем участников
            kanbanSidebar.querySelector('.assigned').innerHTML = '';
            kanbanSidebar
                .querySelector('.assigned')
                .insertAdjacentHTML(
                    'afterbegin',
                    renderAvatar(avatars, false, 'sm', '2', avatars)
                );

        } catch (error) {
            console.error('Ошибка:', error.message);
        }
    }

    // Форматируем дату
    function formatDate(dateString) {
        const date = new Date(dateString);
        return `${date.getDate()} ${date.toLocaleString('en', { month: 'long' })}, ${date.getFullYear()}`;
    }

    // Получаем текущую дату
    function getCurrentDate() {
        const today = new Date();
        return `${today.getDate()} ${today.toLocaleString('en', { month: 'long' })}, ${today.getFullYear()}`;
    }

    // Создаем ссылку для изображения
    function createImageLink(src, id) {
        const link = document.createElement('a');
        link.href = src;
        link.target = '_blank';
        link.style.display = 'inline-block';
        link.style.margin = '5px';

        const img = document.createElement('img');
        img.src = src;
        img.alt = id;
        img.title = 'Файл #'+id;
        img.style.maxWidth = '100px';
        img.style.maxHeight = '60px';
        img.style.cursor = 'pointer';

        link.appendChild(img);
        return link;
    }

    // Создаем ссылку для документа
    function createDocumentLink(href, id) {
        const link = document.createElement('a');
        link.href = href;
        link.alt = id;
        link.title = 'Файл #'+id;
        link.target = '_blank';
        link.textContent = 'Document';
        link.style.display = 'block';
        link.style.margin = '5px';

        const icon = document.createElement('i');
        icon.className = 'fas fa-file-alt';
        icon.style.marginRight = '5px';

        link.prepend(icon);
        return link;
    }

    // Проверяем, есть ли параметр task в URL
    const taskId = getUrlParameter('task');
    if (taskId) {
        await openTaskModal(taskId);
    }

  //! TODO: Update Event label and guest code to JS once select removes jQuery dependency
  // select2
  if (select2.length) {
    function renderLabels(option) {
      if (!option.id) {
        return option.text;
      }
      var $badge = "<div class='badge " + $(option.element).data('color') + " rounded-pill'> " + option.text + '</div>';
      return $badge;
    }

    select2.each(function () {
      var $this = $(this);
      select2Focus($this);
      $this.wrap("<div class='position-relative'></div>").select2({
        placeholder: 'Select Label',
        dropdownParent: $this.parent(),
        templateResult: renderLabels,
        templateSelection: renderLabels,
        escapeMarkup: function (es) {
          return es;
        }
      });
    });
  }

  // Comment editor
  if (commentEditor) {
    new Quill(commentEditor, {
      modules: {
        toolbar: '.comment-toolbar'
      },
      placeholder: 'Write a Comment... ',
      theme: 'snow'
    });
  }

  // Render board dropdown
  function renderBoardDropdown() {
    return (
      "<div class='dropdown'>" +
      "<i class='dropdown-toggle ri-more-2-line ri-20px cursor-pointer' id='board-dropdown' data-bs-toggle='dropdown' aria-haspopup='true' aria-expanded='false'></i>" +
      "<div class='dropdown-menu dropdown-menu-end' aria-labelledby='board-dropdown'>" +
      "<a class='dropdown-item delete-board' href='javascript:void(0)'> <i class='ri-delete-bin-7-line'></i> <span class='align-middle'>Delete</span></a>" +
      "<a class='dropdown-item' href='javascript:void(0)'><i class='ri-edit-2-fill'></i> <span class='align-middle'>Rename</span></a>" +
      "<a class='dropdown-item' href='javascript:void(0)'><i class='ri-archive-line'></i> <span class='align-middle'>Archive</span></a>" +
      '</div>' +
      '</div>'
    );
  }
  // Render item dropdown
  function renderDropdown() {
    return (
      "<div class='dropdown kanban-tasks-item-dropdown'>" +
      "<i class='dropdown-toggle ri-more-2-line' id='kanban-tasks-item-dropdown' data-bs-toggle='dropdown' aria-haspopup='true' aria-expanded='false'></i>" +
      "<div class='dropdown-menu dropdown-menu-end' aria-labelledby='kanban-tasks-item-dropdown'>" +
      "<a class='dropdown-item' href='javascript:void(0)'>Скопировать ссылку</a>" +
      "<a class='dropdown-item' href='javascript:void(0)'>Дублировать</a>" +
      "<a class='dropdown-item delete-task' href='javascript:void(0)'>Удалить</a>" +
      '</div>' +
      '</div>'
    );
  }
  // Render header
  function renderHeader(color, text) {
    return (
      "<div class='d-flex justify-content-between flex-wrap align-items-center mb-2'>" +
      "<div class='item-badges d-flex'> " +
      "<div class='badge rounded-pill bg-label-" +
      color +
      "'> " +
      text +
      '</div>' +
      '</div>' +
      renderDropdown() +
      '</div>'
    );
  }

  // Render avatar
    function renderAvatar(images, pullUp, size, margin, members) {
        var $transition = pullUp ? ' pull-up' : '',
            $size = size ? 'avatar-' + size : '',
            member = members == undefined ? [] : members.split(',');

        return images == undefined
            ? ' '
            : images
                .split(',')
                .map(function (img, index, arr) {
                    var $margin = margin && index !== arr.length - 1 ? ' me-' + margin : '';

                    // Генерация инициалов
                    var initials = member[index]
                        ? member[index].trim().split(' ').map(word => word[0]).join('').toLowerCase()
                        : '';

                    return (
                        "<div class='avatar " +
                        $size +
                        $margin +
                        "'" +
                        "data-bs-toggle='tooltip' data-bs-placement='top'" +
                        "title='" +
                        member[index] +
                        "'" +
                        '>' +
                        "<span class='avatar-initial rounded-circle bg-primary'>" +
                        initials +
                        "</span>" +
                        '</div>'
                    );
                })
                .join(' ');
    }

  // Render footer
  function renderFooter(attachments, comments, assigned, members) {
    return (
      "<div class='d-flex justify-content-between align-items-center flex-wrap mt-2'>" +
      "<div> <span class='align-middle me-3'><i class='ri-attachment-2 ri-20px me-1'></i>" +
      "<span class='attachments'>" +
      attachments +
      '</span>' +
      "</span> " +
      // "<span class='align-middle'><i class='ri-wechat-line ri-20px me-1'></i>" +
      // '<span> ' +
      // comments +
      // ' </span>' +
      // '</span>' +
      '</div>' +
      "<div class='avatar-group d-flex align-items-center assigned-avatar'>" +
      renderAvatar(assigned, true, 'xs', null, members) +
      '</div>' +
      '</div>'
    );
  }
  // Init kanban
  const kanban = new jKanban({
    element: '.kanban-wrapper',
    gutter: '12px',
    widthBoard: '250px',
    dragItems: true,
    boards: boards,
    dragBoards: true,
    addItemButton: true,
    buttonContent: '+ Добавить задачу',
    itemAddOptions: {
      enabled: true, // add a button to board for easy item creation
      content: '+ Добавить задачу', // text or html content of the board button
      class: 'kanban-title-button btn btn-default btn-md shadow-none text-capitalize fw-normal text-heading', // default class of the button
      footer: false // position the button on footer
    },
    click: function (el) {
      let element = el;
      let title = element.getAttribute('data-eid')
          ? element.querySelector('.kanban-text').textContent
          : element.textContent,
        date = element.getAttribute('data-due-date'),
        dateObj = new Date(),
        year = dateObj.getFullYear(),
        dateToUse = date
          ? date + ', ' + year
          : dateObj.getDate() + ' ' + dateObj.toLocaleString('en', { month: 'long' }) + ', ' + year,
        label = element.getAttribute('data-badge-text'),
        avatars = element.getAttribute('data-assigned');
        let description = element.getAttribute('data-description');
        if(description === 'null'){
            description = '';
        }
        // Файлы
        let files = element.getAttribute('data-files'); // Получаем строку с файлами
        // Находим div, куда будем выводить файлы
        let filesDiv = document.querySelector('#files');

        let taskID = element.getAttribute('data-eid');
        loadResponsibles()
            .then(responsibles => {
                // Заполняем select
                populateSelect(responsibles);

                // Выбираем ответственных
                const selectElement = document.querySelector('#assignedSelect');
                const membersString = el.getAttribute('data-members');
                selectAssignedMembers(selectElement, membersString);
            })
            .catch(error => {
                console.error('Ошибка при загрузке ответственных:', error.message);
            });

        document.querySelector('#updateTask').setAttribute('data-id', taskID)
        document.querySelector('#deleteTask').setAttribute('data-id', taskID)

        if (files) {
            // Разделяем строку на массив путей к файлам
            let fileArray = files.split(',');

            // Очищаем содержимое div перед добавлением новых файлов
            filesDiv.innerHTML = '';

            // Проходим по каждому файлу
            fileArray.forEach(file => {
                let trimmedFile = file.trim(); // Убираем лишние пробелы
                // Определяем тип файла по расширению
                if (/\.(jpg|jpeg|png|gif)$/i.test(file)) {
                    let link = document.createElement('a');
                    link.href = trimmedFile;
                    link.target = '_blank'; // Открываем в новой вкладке
                    link.style.display = 'inline-block'; // Делаем ссылку блочным элементом
                    link.style.margin = '5px'; // Опционально: добавляем отступы

                    let img = document.createElement('img');
                    img.src = trimmedFile; // Устанавливаем путь к изображению
                    img.style.maxWidth = '100px'; // Ограничиваем размер изображения
                    img.style.cursor = 'pointer'; // Добавляем указатель мыши

                    link.appendChild(img); // Добавляем изображение внутрь ссылки
                    filesDiv.appendChild(link); // Добавляем ссылку в div
                } else {
                    // Если это документ, создаем элемент <a> с иконкой
                    let link = document.createElement('a');
                    link.href = file.trim(); // Убираем лишние пробелы
                    link.target = '_blank'; // Открываем ссылку в новой вкладке
                    link.textContent = 'Документ'; // Текст ссылки
                    link.style.display = 'block'; // Делаем ссылку блочным элементом
                    link.style.margin = '5px'; // Опционально: добавляем отступы

                    // Добавляем иконку (например, FontAwesome)
                    let icon = document.createElement('i');
                    icon.className = 'fas fa-file-alt'; // Иконка документа (FontAwesome)
                    icon.style.marginRight = '5px'; // Отступ между иконкой и текстом
                    link.prepend(icon); // Добавляем иконку перед текстом

                    filesDiv.appendChild(link);
                }
            });
        }
        // /Файлы

      // Show kanban offcanvas
      kanbanOffcanvas.show();

      // To get data on sidebar
      kanbanSidebar.querySelector('#title').value = title;
      kanbanSidebar.querySelector('#due-date').nextSibling.value = dateToUse;
      kanbanSidebar.querySelector('#description').value = description;

      // ! Using jQuery method to get sidebar due to select2 dependency
      $('.kanban-update-item-sidebar').find(select2).val(label).trigger('change');

      // Remove & Update assigned
      kanbanSidebar.querySelector('.assigned').innerHTML = '';
      kanbanSidebar
        .querySelector('.assigned')
        .insertAdjacentHTML(
          'afterbegin',
          renderAvatar(avatars, false, 'sm', '2', el.getAttribute('data-members'))
        );

      // Load activity

        fetch(`/api/task/${taskID}/activity?uid=${uid}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Ошибка HTTP: ${response.status}`);
                }
                return response.json();
            })
            .then(activities => {
                // Находим контейнер для активности
                const activityContainer = document.querySelector('#tab-activity');
                activityContainer.innerHTML = ''; // Очищаем предыдущее содержимое

                // Проходим по каждому элементу активности
                activities.forEach(activity => {
                    // Создаем HTML-структуру для одной записи активности
                    const activityHTML = `
                    <div class="media mb-4 d-flex align-items-center">
                        <div class="avatar me-3 flex-shrink-0">
                            <a href="/dashboard/users/${activity.user.id}">
                                <span class="avatar-initial bg-label-success rounded-circle">${getInitials(activity.user)}</span>
                            </a>
                        </div>
                        <div class="media-body ms-1">
                            <p class="mb-0">
                                <a href="/dashboard/task/${activity.activitable_id}">
                                    <span>${activity.user.first_name || activity.user.username}</span> ${activity.action}
                                </a>
                            </p>
                            <small class="text-muted">${formatDate(activity.created_at)}</small>
                        </div>
                    </div>
                `;

                    // Добавляем запись в контейнер
                    activityContainer.insertAdjacentHTML('beforeend', activityHTML);
                });
            })
            .catch(error => {
                console.error('Ошибка при загрузке активности:', error.message);
            });
        /////////////
    },

    buttonClick: function (el, boardId) {
      const addNew = document.createElement('form');
      addNew.setAttribute('class', 'new-item-form');
      addNew.innerHTML =
        '<div class="mb-4">' +
        '<textarea class="form-control add-new-item" rows="2" placeholder="Задача" autofocus required></textarea>' +
        '</div>' +
        '<div class="mb-4">' +
        '<button type="submit" class="btn btn-primary btn-sm me-4">Добавить</button>' +
        '<button type="button" class="btn btn-outline-secondary btn-sm cancel-add-item">Закрыть</button>' +
        '</div>';
      kanban.addForm(boardId, addNew);

        // Находим кнопку "Закрыть" и добавляем обработчик события
        const cancelButton = addNew.querySelector('.cancel-add-item');
        cancelButton.addEventListener('click', function () {
            // Удаляем родительский элемент формы
            addNew.remove();
        });

      addNew.addEventListener('submit', function (e) {
        e.preventDefault();

          try {
              // Извлекаем project_id из URL
              const path = window.location.pathname;
              const match = path.match(/\/dashboard\/project\/(\d+)/);
              const projectId = match ? match[1] : null;

              // Формируем URL для запроса
              let apiUrl = `/api/task?uid=${uid}`;

              if (!projectId) {
                  console.error('ID проекта не найден в URL');
                  return;
              }
              const title = e.target[0].value; // Значение первого поля формы

              // Формируем тело запроса
              const data = {
                  project_id: projectId,
                  column_id: boardId.replace("board-", ""),
                  title: title
              };

              // Отправляем POST-запрос
              const response =  fetch(apiUrl, {
                  method: 'POST',
                  headers: {
                      'Content-Type': 'application/json',
                  },
                  body: JSON.stringify(data),
              }).then(response => {
                  if (!response.ok) {
                      throw new Error('Network response was not ok');
                  }
                  return response.json(); // Если сервер возвращает JSON, парсим его
              })
                  .then(result => {
                      const currentBoard = [].slice.call(
                          document.querySelectorAll(`.kanban-board[data-id="${boardId}"] .kanban-item`)
                      );

                      kanban.addElement(boardId, {
                          title: "<span class='kanban-text'>" + title + '</span>',
                          id: boardId + '-' + currentBoard.length + 1,
                      });

                      // Добавляем выпадающий список в новые карточки
                      const kanbanText = [].slice.call(
                          document.querySelectorAll(`.kanban-board[data-id="${boardId}"] .kanban-text`)
                      );
                      kanbanText.forEach(function (e) {
                          e.insertAdjacentHTML('beforebegin', renderDropdown());
                      });

                      activity("создал задачу '"+result.task.title+"'", result.task.id);

                      const elementToRemove = this.parentElement.parentElement;
                      // Удаляем форму
                      if (elementToRemove) {
                          elementToRemove.remove();
                      }

                  })
                  .catch(error => {
                      console.error('There was a problem with the fetch operation:', error);
                  });

              if (!response.ok) {
                  console.log(response)
              }

              const newTaskDropdown = [].slice.call(document.querySelectorAll('.kanban-item .kanban-tasks-item-dropdown'));
              if (newTaskDropdown) {
                  newTaskDropdown.forEach(function (e) {
                      e.addEventListener('click', function (el) {
                          el.stopPropagation();
                      });
                  });
              }

              // delete tasks for new boards
              const deleteTaskButtons = document.querySelectorAll(
                  `.kanban-board[data-id="${boardId}"] .delete-task`
              );
              deleteTask.forEach(function (e) {
                  e.addEventListener('click', function () {
                      const id = this.closest('.kanban-item').getAttribute('data-eid');
                      kanban.removeElement(id);
                  });
              });
              addNew.remove();

              // Remove form on clicking cancel button
              addNew.querySelector('.cancel-add-item').addEventListener('click', function (e) {
                  addNew.remove();
              });

          } catch (error) {
              console.error('Ошибка при создании задачи:', error);
          }
      });
    },

      dropEl: function(el, target, source, sibling) {
          // 1. Получаем данные задачи и колонок
          const taskId = el.getAttribute('data-eid');
          const sourceColumnId = source.parentNode.dataset.id; // Исходная колонка
          const targetColumnId = target.parentNode.dataset.id; // Целевая колонка

          const targetNumber = targetColumnId.replace(/board-/g, '');
          const targetColumn = document.querySelector(`.kanban-board[data-id="${targetColumnId}"]`);

          // 2. Получаем project_id и uid из URL
          const path = window.location.pathname;
          const match = path.match(/\/dashboard\/project\/(\d+)/);
          const projectId = match ? match[1] : null;

          // 3. Формируем запрос
          fetch(`/api/task/${taskId}/move?uid=${uid}`, {
              method: 'PUT',
              headers: {
                  'Content-Type': 'application/json',
              },
              body: JSON.stringify({
                  column_id: targetNumber,
                  project_id: projectId
              })
          })
              .then(response => {
                  if (!response.ok) throw new Error('Ошибка перемещения');
                  return response.json();
              })
              .then(data => {
                  const targetDrag = targetColumn.querySelector('.kanban-drag');
                  if (targetDrag && targetDrag.children.length > 0) {
                      targetDrag.style.height = 'auto'; // Сбрасываем высоту
                      const targetHeader = targetColumn.querySelector('.kanban-board-header');
                      if (targetHeader) {
                          const targetButton = targetHeader.querySelector('.kanban-title-button');
                          if (targetButton) {
                              targetButton.style.bottom = '0'; // Сбрасываем bottom
                          }
                      }
                  }

                  activity('переместил задачу в колонку #'+targetNumber,taskId);
              })
              .catch(error => {
                  console.error('Ошибка:', error);
                  // Возвращаем задачу обратно при ошибке
                  kanban.moveElement(taskId, sourceColumnId);
              });
      }
  });

  // Исправляем ошибку связанную с высотой колонки!
    document.querySelectorAll('.kanban-drag').forEach(dragContainer => {
        // Проверяем, есть ли дочерние элементы внутри .kanban-drag
        if (dragContainer.children.length === 0) {
            // Устанавливаем высоту для пустого .kanban-drag
            dragContainer.style.height = '500px';

            // Находим родительскую колонку
            const parentBoard = dragContainer.closest('.kanban-board');

            if (parentBoard) {
                // Находим заголовок колонки (.kanban-board-header)
                const boardHeader = parentBoard.querySelector('.kanban-board-header');
                if (boardHeader) {
                    // Находим кнопку внутри заголовка (.kanban-title-button)
                    const titleButton = boardHeader.querySelector('.kanban-title-button');
                    if (titleButton) {
                        // Устанавливаем bottom для кнопки
                        titleButton.style.bottom = '500px';
                    }
                }
            }
        }
    });

  // Kanban Wrapper scrollbar
  if (kanbanWrapper) {
    new PerfectScrollbar(kanbanWrapper);
  }

  const kanbanContainer = document.querySelector('.kanban-container'),
    kanbanTitleBoard = [].slice.call(document.querySelectorAll('.kanban-title-board')),
    kanbanItem = [].slice.call(document.querySelectorAll('.kanban-item'));

  // Render custom items
  if (kanbanItem) {
    kanbanItem.forEach(function (el) {
      const element = "<span class='kanban-text'>" + el.textContent + '</span>';
      let img = '';
      if (el.getAttribute('data-image') !== null) {
        img =
          "<img class='img-fluid mb-2 rounded-4' src='" +
          assetsPath +
          'img/elements/' +
          el.getAttribute('data-image') +
          "'>";
      }
      el.textContent = '';
      if (el.getAttribute('data-badge') !== undefined && el.getAttribute('data-badge-text') !== undefined) {
        el.insertAdjacentHTML(
          'afterbegin',
          renderHeader(el.getAttribute('data-badge'), el.getAttribute('data-badge-text')) + img + element
        );
      }
      if (
        el.getAttribute('data-comments') !== undefined ||
        el.getAttribute('data-due-date') !== undefined ||
        el.getAttribute('data-assigned') !== undefined
      ) {
        el.insertAdjacentHTML(
          'beforeend',
          renderFooter(
            el.getAttribute('data-attachments'),
            el.getAttribute('data-comments'),
            el.getAttribute('data-assigned'),
            el.getAttribute('data-members')
          )
        );
      }
    });
  }

  // To initialize tooltips for rendered items
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });

  // prevent sidebar to open onclick dropdown buttons of tasks
  const tasksItemDropdown = [].slice.call(document.querySelectorAll('.kanban-tasks-item-dropdown'));
  if (tasksItemDropdown) {
    tasksItemDropdown.forEach(function (e) {
      e.addEventListener('click', function (el) {
        el.stopPropagation();
      });
    });
  }

  // Toggle add new input and actions add-new-btn
  if (kanbanAddBoardBtn) {
    kanbanAddBoardBtn.addEventListener('click', () => {
      kanbanAddNewInput.forEach(el => {
        el.value = '';
        el.classList.toggle('d-none');
      });
    });
  }

  // Render add new inline with boards
  if (kanbanContainer) {
    kanbanContainer.appendChild(kanbanAddNewBoard);
  }

  // Makes kanban title editable for rendered boards
  if (kanbanTitleBoard) {
    kanbanTitleBoard.forEach(function (elem) {
      elem.addEventListener('mouseenter', function () {
        this.contentEditable = 'true';
      });

      // Appends delete icon with title
      elem.insertAdjacentHTML('afterend', renderBoardDropdown());
    });
  }

  // To delete Board for rendered boards
  const deleteBoards = [].slice.call(document.querySelectorAll('.delete-board'));
  if (deleteBoards) {
    deleteBoards.forEach(function (elem) {
      elem.addEventListener('click', function () {
        const id = this.closest('.kanban-board').getAttribute('data-id');
        kanban.removeBoard(id);
      });
    });
  }

  // Delete task for rendered boards
  const deleteTask = [].slice.call(document.querySelectorAll('.delete-task'));
  if (deleteTask) {
    deleteTask.forEach(function (e) {
      e.addEventListener('click', function () {
        const id = this.closest('.kanban-item').getAttribute('data-eid');
        kanban.removeElement(id);
      });
    });
  }

  // Cancel btn add new input
  const cancelAddNew = document.querySelector('.kanban-add-board-cancel-btn');
  if (cancelAddNew) {
    cancelAddNew.addEventListener('click', function () {
      kanbanAddNewInput.forEach(el => {
        el.classList.toggle('d-none');
      });
    });
  }

  // Add new board
  if (kanbanAddNewBoard) {
    kanbanAddNewBoard.addEventListener('submit', function (e) {
      e.preventDefault();
      const thisEle = this,
        value = thisEle.querySelector('.form-control').value,
        id = value.replace(/\s+/g, '-').toLowerCase();
      kanban.addBoards([
        {
          id: id,
          title: value
        }
      ]);

      // Adds delete board option to new board, delete new boards & updates data-order
      const kanbanBoardLastChild = document.querySelectorAll('.kanban-board:last-child')[0];
      if (kanbanBoardLastChild) {
        const header = kanbanBoardLastChild.querySelector('.kanban-title-board');
        header.insertAdjacentHTML('afterend', renderBoardDropdown());

        // To make newly added boards title editable
        kanbanBoardLastChild.querySelector('.kanban-title-board').addEventListener('mouseenter', function () {
          this.contentEditable = 'true';
        });
      }

      // Add delete event to delete newly added boards
      const deleteNewBoards = kanbanBoardLastChild.querySelector('.delete-board');
      if (deleteNewBoards) {
        deleteNewBoards.addEventListener('click', function () {
          const id = this.closest('.kanban-board').getAttribute('data-id');
          kanban.removeBoard(id);
        });
      }

      // Remove current append new add new form
      if (kanbanAddNewInput) {
          // ajax POST column
          alert('Скоро будет готово')
        kanbanAddNewInput.forEach(el => {
          el.classList.add('d-none');
        });
      }

      // To place inline add new btn after clicking add btn
      if (kanbanContainer) {
        kanbanContainer.appendChild(kanbanAddNewBoard);
      }
    });
  }

  // Clear comment editor on close
  kanbanSidebar.addEventListener('hidden.bs.offcanvas', function () {
    //kanbanSidebar.querySelector('.ql-editor').firstElementChild.innerHTML = '';
    document.querySelector('.taskDescriptionTextArea').firstElementChild.innerHTML = '';
  });

  // Re-init tooltip when offcanvas opens(Bootstrap bug)
  if (kanbanSidebar) {
    kanbanSidebar.addEventListener('shown.bs.offcanvas', function () {
      const tooltipTriggerList = [].slice.call(kanbanSidebar.querySelectorAll('[data-bs-toggle="tooltip"]'));
      tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
      });
    });
  }

    // Функция для получения инициалов пользователя
    function getInitials(user) {
        const name = user.first_name || user.username;
        return name.charAt(0).toUpperCase(); // Первая буква имени или username
    }

// Функция для форматирования даты
    function formatDate(dateString) {
        const date = new Date(dateString);
        const today = new Date();
        const isToday = date.getDate() === today.getDate() &&
            date.getMonth() === today.getMonth() &&
            date.getFullYear() === today.getFullYear();

        const formattedTime = date.toLocaleTimeString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true });

        return isToday ? `Today ${formattedTime}` : `${date.toLocaleDateString()} ${formattedTime}`;
    }

  function activity(text, task_id){
      // Создаем активность
      fetch('/api/me?uid='+uid, {
          method: 'GET',
          headers: {
              'Content-Type': 'application/json',
          }
      })
          .then(response => {
              if (!response.ok) {
                  throw new Error('Ошибка при получении данных пользователя');
              }
              return response.json();
          })
          .then(me => {
              // Второй запрос: POST /api/activity
              let actionText = "Пользователь " + me.username + " "+text;
              if (actionText.length > 252) {
                  actionText = actionText.slice(0, 252) + "..";
              }
              const activityData = {
                  action: actionText,
                  activitable_type: "App\\Models\\Task",
                  activitable_id: task_id
              };

              return fetch('/api/activity?uid='+uid, {
                  method: 'POST',
                  headers: {
                      'Content-Type': 'application/json'
                  },
                  body: JSON.stringify(activityData)
              });
          })
          .then(response => {
              if (!response.ok) {
                  throw new Error('Ошибка при отправке активности');
              }
              return response.json(); // Парсим ответ второго запроса
          })

          .catch(error => {
              console.error('Произошла ошибка:', error.message);
          });
  }

    function formatDate(dateString) {
        let date = new Date(dateString);

        // Извлечение года, месяца и дня
        let year = date.getFullYear();
        let month = String(date.getMonth() + 1).padStart(2, '0'); // Месяцы начинаются с 0
        let day = String(date.getDate()).padStart(2, '0');

        // Формирование строки в формате Y-m-d
        return `${year}-${month}-${day}`;
    }

  updateTaskButton.addEventListener('click', function () {
        let taskID = this.getAttribute('data-id');

        let title = kanbanSidebar.querySelector('#title').value;
        let rawDate = kanbanSidebar.querySelector('#due-date').nextSibling.value;
        let description = kanbanSidebar.querySelector('#description').value;
        //let assigned = kanbanSidebar.querySelector('#assignedSelect').value;
        let assignedSelect = kanbanSidebar.querySelector('#assignedSelect');
        let assignedValues = Array.from(assignedSelect.selectedOptions).map(option => option.value);

        let attachmentsInput = kanbanSidebar.querySelector('#attachments');
        let attachments = attachmentsInput.files;
        let formData = new FormData();
        let formattedDate = formatDate(rawDate);

        // Добавление текстовых данных
        formData.append('project_id', title);
        formData.append('column_id', title);

        formData.append('title', title);
        formData.append('date', formattedDate);
        formData.append('description', description);
        assignedValues.forEach(value => {
            formData.append('responsible[]', value);
        });

        // Добавление файлов
        for (let i = 0; i < attachments.length; i++) {
            formData.append('files[]', attachments[i]); // 'attachments[]' для массива файлов
        }

        fetch(`/api/get-column-id/${taskID}?uid=${uid}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Ошибка HTTP: ${response.status}`);
                }
                return response.json();
            }).then(data => {
                formData.append('column_id', data.id);

                const path = window.location.pathname;
                const match = path.match(/\/dashboard\/project\/(\d+)/);
                const projectId = match ? match[1] : null;

                if (!projectId) {
                    console.error('ID проекта не найден в URL');
                    return;
                }
                formData.append('project_id', projectId);

                fetch(`/api/task/web/${taskID}?uid=${uid}`, {
                    method: 'POST',
                    body: formData,
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`Ошибка HTTP: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Переименовать!
                        const element = document.querySelector('.kanban-drag div[data-eid="'+data.task.id+'"]');
                        if (element) {
                            const spanElement = element.querySelector('span.kanban-text');
                            if (spanElement) {
                                spanElement.textContent = data.task.title;
                            }
                        }
                        activity("обновил задачу "+data.task.title, data.task.id);
                    })
                    .catch(error => {
                        console.error('Ошибка при отправке данных:', error);
                    });
        })
            .catch(error => {
                console.error('Ошибка при отправке данных:', error);
            });
    });

    deleteTaskButton.addEventListener('click', function () {
        let taskID = this.getAttribute('data-id');
        fetch(`/api/task/${taskID}?uid=${uid}`, {
            method: 'DELETE',
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Ошибка HTTP: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                const elementToRemove = document.querySelector('.kanban-drag div[data-eid="'+data.id+'"]');
                // Если элемент найден, удаляем его
                if (elementToRemove) {
                    elementToRemove.remove();
                } else {
                    console.log('Элемент не найден');
                }
                activity("удалил задачу #"+data.id, data.id);
            })
            .catch(error => {
                console.error('Ошибка при отправке данных:', error);
            });
    });

    // Отображение ответсвенных из Select
        const selectElement = document.getElementById('assigned');
        const assignedContainer = document.querySelector('.assigned');

        // Функция для создания аватара
        function createAvatar(value, text) {
            const firstLetter = text.charAt(0).toUpperCase(); // Первая буква имени
            const avatarDiv = document.createElement('div');
            avatarDiv.className = 'avatar avatar-sm me-2';
            avatarDiv.setAttribute('data-bs-toggle', 'tooltip');
            avatarDiv.setAttribute('data-bs-placement', 'top');
            avatarDiv.setAttribute('data-bs-original-title', text);

            const span = document.createElement('span');
            span.className = 'avatar-initial rounded-circle bg-primary';
            span.textContent = firstLetter;

            avatarDiv.appendChild(span);
            return avatarDiv;
        }

        // Функция для обновления блока .assigned
        function updateAssigned() {
            // Очищаем контейнер
            assignedContainer.innerHTML = '';

            // Получаем выбранные опции
            const selectedOptions = Array.from(selectElement.selectedOptions);

            // Создаем аватары для выбранных опций
            selectedOptions.forEach(option => {
                const value = option.value;
                const text = option.textContent.trim();
                const avatar = createAvatar(value, text);
                assignedContainer.appendChild(avatar);
            });

            // Инициализируем тултипы
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
        }
        // Слушаем событие изменения в select
        selectElement.addEventListener('change', updateAssigned);
        // Инициализация при загрузке страницы (если есть выбранные элементы)
        updateAssigned();


        // Подгружаем ответсвенных
        function loadResponsibles() {
            const path = window.location.pathname;
            const match = path.match(/\/dashboard\/project\/(\d+)/);
            const projectId = match ? match[1] : null;

            if (!projectId) {
                console.error('ID проекта не найден в URL');
                return;
            }

            return fetch(`/api/project/${projectId}/responsible?uid=${uid}`)
                .then(response => {
                    // Проверяем статус ответа
                    if (!response.ok) {
                        throw new Error(`Ошибка HTTP: ${response.status}`);
                    }
                    // Парсим JSON-ответ
                    return response.json();
                })
                .then(responsibles => {
                    console.log('Загружены ответственные:', responsibles);
                    return responsibles; // Возвращаем данные для дальнейшего использования
                })
                .catch(error => {
                    console.error('Ошибка при загрузке ответственных:', error.message);
                    return []; // Возвращаем пустой массив в случае ошибки
                });
        }
        function populateSelect(responsibles) {
            const select = document.querySelector('#assignedSelect');
            select.innerHTML = ''; // Очищаем предыдущие опции

            if (!Array.isArray(responsibles)) {
                console.error('responsibles не является массивом:', responsibles);
                return;
            }

            responsibles.forEach(responsible => {
                const option = document.createElement('option');
                option.value = responsible.id; // ID для value
                option.textContent = responsible.first_name || responsible.username || 'Без имени'; // Текстовое содержимое
                select.appendChild(option);
            });
        }

        function selectAssignedMembers(selectElement, membersString) {
            if (!membersString) return;

            const members = membersString.split(',').map(member => member.trim());
            Array.from(selectElement.options).forEach(option => {
                if (members.includes(option.textContent)) {
                    option.selected = true;
                }
            });
        }
})();
