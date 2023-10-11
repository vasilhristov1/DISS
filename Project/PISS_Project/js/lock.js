//set menu for rows and cols
function setContextMenus() {
    var rows = document.querySelectorAll(".row-index");
    var columns = document.querySelectorAll(".column-index");

    columns.forEach(element => {
        element.addEventListener("contextmenu", e => {
            e.preventDefault();
            openColumnContextMenu(element);
        });
    });

    rows.forEach(element => {
        element.addEventListener("contextmenu", () => {
            openRowContextMenu(element);
        });
    });
};

// open column's context menu
function openColumnContextMenu(element) {
    if (contextMenu.classList.contains("context-menu-hidden")) {
        currentTd = element;
        contextMenu.classList.toggle("context-menu-hidden");
        fixUpColumns(element);
        let position = currentTd.getBoundingClientRect();
        let x = position.right;
        let y = position.top + window.scrollY;
        contextMenu.style.position = "absolute";
        contextMenu.style.top = y + 'px';
        contextMenu.style.left = x + 'px';
    }
    else {
        if (currentTd != element) {
            currentTd = element;
            fixUpColumns(element);
            let position = currentTd.getBoundingClientRect();
            let x = position.right;
            let y = position.top + window.scrollY;
            contextMenu.style.position = "absolute";
            contextMenu.style.top = y + 'px';
            contextMenu.style.left = x + 'px';
        }
    }
}

function fixUpColumns(element) {
    removeInsertRowOptions(contextMenu);

    if (contextMenu.querySelector('#insert-column') == null) {
        let insertColumn = document.createElement('span');
        insertColumn.setAttribute('id','insert-column');
        insertColumn.textContent = "Insert column";
        insertColumn.addEventListener('click', e => {
            let currentColumn = currentTd;
            conn.send("insertColumn-"+currentColumn.cellIndex);
            insertNewColumn(currentColumn.cellIndex);
            fixFollowingColumns(currentColumn.cellIndex);
        });

        contextMenu.appendChild(insertColumn);
    }

    var column = document.querySelectorAll(`.${element.innerText[0]}`);

    column.forEach(el => {
        if (el.hasAttribute("contenteditable")) {
            cellLock.innerHTML = "Lock";
        }
        else {
            cellLock.innerHTML = "Unlock";
        }
    });
}

// open row's context menu
function openRowContextMenu(element) {
    if (contextMenu.classList.contains("context-menu-hidden")) {
        currentTd = element;
        contextMenu.classList.toggle("context-menu-hidden");
        fixUpRows(element);
    }
    else {
        if (currentTd != element) {
            currentTd = element;
            fixUpRows(element);
        }
    }
}

function fixUpRows(element) {
    removeInsertColumnOptions(contextMenu);
    let position = element.getBoundingClientRect();
    let x = position.right;
    let y = position.top + window.scrollY;
    contextMenu.style.position = "absolute";
    contextMenu.style.top = y + 'px';
    contextMenu.style.left = x + 'px';

    if (contextMenu.querySelector('#insert-row') == null) {
        let insertRow = document.createElement('span');
        insertRow.setAttribute('id','insert-row');
        insertRow.textContent = "Insert row";
        insertRow.addEventListener('click', e => {
            let currentRow = currentTd.parentElement;
            conn.send("insertRow-"+currentRow.rowIndex);
            insertNewRow(currentRow.rowIndex);
            fixFollowingRows(currentRow.rowIndex);
        });

        contextMenu.appendChild(insertRow);
    }

    var nthElement = Number(element.firstChild.nodeValue) + 1;
    var row = document.querySelectorAll(`tr:nth-of-type(${nthElement})`)[0].childNodes;
    row.forEach(el => {
        if (el.classList.contains("row-index")) {
            return;
        }
        if (el.hasAttribute("contenteditable")) {
            cellLock.innerHTML = "Lock";
        }
        else {
            cellLock.innerHTML = "Unlock";
        }
    });
}

// locking the whole column
function columnLockUnlock(column) {
    if (currentTd.classList.contains("columns-locked")) {
        currentTd.classList.remove("columns-locked");
        column.forEach(el => {
            el.setAttribute('contenteditable', "");
            el.classList.remove('locked-cell');
            cellLock.innerHTML = "Lock";
            conn.send("changeClass-" + el.id + "-" + el.classList.value);
        });
    } else {
        currentTd.classList.add("columns-locked");
        column.forEach(el => {
            if (el.hasAttribute("contenteditable")) {
                el.removeAttribute('contenteditable');
                el.classList.add('locked-cell');
                cellLock.innerHTML = "Unlock";
                conn.send("changeClass-" + el.id + "-" + el.classList.value);
            }
        });
    }
}

// locking the whole row
function rowLockUnlock(row) {
    if (currentTd.classList.contains("rows-locked")) {
        currentTd.classList.remove("rows-locked");
        row.forEach(el => {
            if (el.classList.contains("row-index")) {
                return;
            }
            else {
                el.setAttribute('contenteditable', "");
                el.classList.remove('locked-cell');
                cellLock.innerHTML = "Lock";
                conn.send("changeClass-" + el.id + "-" + el.classList.value);
            }
        });
    } else {
        currentTd.classList.add("rows-locked");
        row.forEach(el => {
            if (el.classList.contains("row-index")) {
                return;
            }
            if (el.hasAttribute("contenteditable")) {
                el.removeAttribute('contenteditable');
                el.classList.add('locked-cell');
                cellLock.innerHTML = "Unlock";
                conn.send("changeClass-" + el.id + "-" + el.classList.value);
            }
        });
    }
}