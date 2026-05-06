  /*side panel resize*/
  const resizer = document.getElementById('sidebarResizer');
  const sidebar = document.getElementById('leftSidebar');

  resizer.addEventListener('mousedown', function (e) {
    e.preventDefault();
    document.addEventListener('mousemove', resize);
    document.addEventListener('mouseup', stopResize);
  });

  function resize(e) {
    const newWidth = e.clientX - sidebar.getBoundingClientRect().left;
    sidebar.style.width = newWidth + 'px';
    sidebar.classList.remove('col-3'); // Remove Bootstrap col to allow manual width
  }

  function stopResize() {
    document.removeEventListener('mousemove', resize);
    document.removeEventListener('mouseup', stopResize);
  }
/*side panel resize*/

/*side panel short list to long list*/
    function list_style() {
    const leftNav = document.querySelector('.left-nav');
    const content = document.querySelector('.content-container');

    if ($('#leftSidebar').css('width')) {
        $('#leftSidebar').css('width', '');
    }

    if (!leftNav.classList.contains('col-3') && !leftNav.classList.contains('col-12')) {
        // Initial fallback: expand left panel full-width
        leftNav.classList.add('col-12');
        leftNav.style.width = '100%';
        $('#short-list').fadeOut(300, function () {
            $('#long-list').fadeIn(300);
        });
        $('.aditional_search').removeClass('d-none');
    } else if (leftNav.classList.contains('col-3')) {
        // Expand panel
        leftNav.classList.remove('col-3');
        leftNav.classList.add('col-12');

        if (content) {
            content.classList.remove('col-9');
            content.classList.add('col-0');
        }

        $('#short-list').fadeOut(300, function () {
            $('#long-list').fadeIn(300);
        });
        $('.aditional_search').removeClass('d-none');
    } else if (leftNav.classList.contains('col-12')) {
        // Collapse panel
        leftNav.classList.remove('col-12');
        leftNav.classList.add('col-3');

        if (content) {
            content.classList.remove('col-0');
            content.classList.add('col-9');
        }

        $('#long-list').fadeOut(300, function () {
            $('#short-list').fadeIn(300);
        });
        $('.aditional_search').addClass('d-none');
    }
}
/*side panel short list to long list*/

/*table resize*/
    document.querySelectorAll('th.resizable').forEach(function (th) {
        const resizer = th.querySelector('.resizer');
        let startX, startWidth;

        resizer.addEventListener('mousedown', function (e) {
            startX = e.pageX;
            startWidth = th.offsetWidth;

            document.addEventListener('mousemove', mousemove);
            document.addEventListener('mouseup', mouseup);
        });

        function mousemove(e) {
            const newWidth = startWidth + (e.pageX - startX);
            th.style.width = newWidth + 'px';
        }

        function mouseup() {
            document.removeEventListener('mousemove', mousemove);
            document.removeEventListener('mouseup', mouseup);
        }
    });
/*table resize*/

/*list page card*/
    document.querySelectorAll('.arrow-toggle').forEach(toggle => {
        toggle.addEventListener('click', () => {
        const card = toggle.closest('.status-card');
        card.classList.toggle('card-expanded');
        });
    });
/*list page card*/



/*table row addrow deleterow*/
  const table = document.getElementById('myTable');
  const contextMenu = document.getElementById('contextMenu');
  let selectedRow = null;

  table.addEventListener('contextmenu', function (e) {
    e.preventDefault();

    const tr = e.target.closest('tr');
    if (!tr || !tr.parentNode.matches('tbody')) return;

    selectedRow = tr;

    contextMenu.style.top = `${e.pageY-60}px`;
    contextMenu.style.left = `${e.pageX+20}px`;
    contextMenu.style.display = 'block';
  });

  // Hide menu on click elsewhere
  document.addEventListener('click', function () {
    contextMenu.style.display = 'none';
  });

  // Add Row Below
document.getElementById('addRow').addEventListener('click', function () {
  if (selectedRow) {
    const newRow = selectedRow.cloneNode(true);

    // Clear all input values
    newRow.querySelectorAll('input').forEach(input => input.value = '');

    // === SECOND COLUMN MODIFICATION ===
    const secondCell = newRow.cells[1]; // assuming second column is index 1
    if (secondCell) {
      // Clear existing content
      const originalSelect = selectedRow.cells[1].querySelector('select');
      const existingName = originalSelect ? originalSelect.name : '';
      secondCell.innerHTML = '';

      // Create new select element
      const select = document.createElement('select');
      select.className = 'form-control noborder';
      select.name = existingName;

      // Append the new select to second cell
      secondCell.appendChild(select);
    }

    

    // Insert the cloned row after the selected one
    selectedRow.parentNode.insertBefore(newRow, selectedRow.nextSibling);

    // Recalculate serial numbers
    const allRows = document.querySelectorAll('#myTable tbody tr');
    allRows.forEach((row, index) => {
      const serialInput = row.cells[0].querySelector('input');
      if (serialInput) {
        serialInput.value = index + 1;
      }
    });
  }
});

  // Delete Row
  document.getElementById('deleteRow').addEventListener('click', function () {
    if (selectedRow && table.rows.length > 2) { // prevent deleting all rows
      selectedRow.remove();
    }
  });
  /*table row addrow deleterow*/

  /*date picker*/
  flatpickr(".date-picker", {
    dateFormat: "d/m/Y", // dd/mm/yyyy
    allowInput: true
  });
  /*date picker*/