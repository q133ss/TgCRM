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
    assetsPath = document.querySelector('html').getAttribute('data-assets-path');

  // Init kanban Offcanvas
  const kanbanOffcanvas = new bootstrap.Offcanvas(kanbanSidebar);

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

        // Извлекаем UID из строки запроса
        const urlParams = new URLSearchParams(window.location.search);
        const uid = urlParams.get('uid'); // uid = "461612832"

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
        console.log('Данные задач:', boards);
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
        if(description == 'null'){
            description = '';
        }
        // Файлы
        let files = element.getAttribute('data-files'); // Получаем строку с файлами
        // Находим div, куда будем выводить файлы
        let filesDiv = document.querySelector('#files');

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
                    link.textContent = 'Document'; // Текст ссылки
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

      addNew.addEventListener('submit', function (e) {
        e.preventDefault();

          try {
              // Извлекаем project_id из URL
              const path = window.location.pathname;
              const match = path.match(/\/dashboard\/project\/(\d+)/);
              const projectId = match ? match[1] : null;

              const urlParams = new URLSearchParams(window.location.search);
              const uid = urlParams.get('uid'); // uid = "461612832"

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
                  column_id: boardId,
                  title: title
              };

              // Отправляем POST-запрос
              const response =  fetch(apiUrl, {
                  method: 'POST',
                  headers: {
                      'Content-Type': 'application/json',
                  },
                  body: JSON.stringify(data),
                  // success: () => {
                  //     alert(111)
                  //     const result = response;
                  //     const currentBoard = [].slice.call(
                  //         document.querySelectorAll('.kanban-board[data-id=' + boardId + '] .kanban-item')
                  //     );
                  //     kanban.addElement(boardId, {
                  //         title: "<span class='kanban-text'>" + title + '</span>',
                  //         id: boardId + '-' + currentBoard.length + 1
                  //     });
                  //
                  //     // add dropdown in new boards
                  //     const kanbanText = [].slice.call(
                  //         document.querySelectorAll('.kanban-board[data-id=' + boardId + '] .kanban-text')
                  //     );
                  //     kanbanText.forEach(function (e) {
                  //         e.insertAdjacentHTML('beforebegin', renderDropdown());
                  //     });
                  // }
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
                          document.querySelectorAll('.kanban-board[data-id=' + boardId + '] .kanban-text')
                      );
                      kanbanText.forEach(function (e) {
                          e.insertAdjacentHTML('beforebegin', renderDropdown());
                      });

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
    kanbanSidebar.querySelector('.ql-editor').firstElementChild.innerHTML = '';
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
})();
