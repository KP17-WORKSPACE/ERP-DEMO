/*side panel resize*/
const resizer = document.getElementById('sidebarResizer');
const sidebar = document.getElementById('leftSidebar');


if (resizer && sidebar) {
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
}


/*side panel resize*/

/*side panel short list to long list*/
// Restore panel state before user sees it

function list_style() {
  const leftNav = document.querySelector('.left-nav');
  const content = document.querySelector('.content-container');

  // if ($('#leftSidebar').css('width')) {
  //   $('#leftSidebar').css('width', '');
  // }

  // Expand from col-3 → col-12
  if (leftNav.classList.contains('col-3')) {
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
    localStorage.setItem("leftNavState", "expanded");
  }
  // Collapse from col-12 → col-3
  else if (leftNav.classList.contains('col-12')) {
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
    localStorage.setItem("leftNavState", "collapsed");
  }
  const state = localStorage.getItem("leftNavState");
}

function list_style_search() {
  const leftNav = document.querySelector('.left-nav');
  const content = document.querySelector('.content-container');
  // if ($('#leftSidebar').css('width')) {
  //   $('#leftSidebar').css('width', '');
  // }

  // Expand from col-3 → col-12
  if (leftNav.classList.contains('col-3')) {
    leftNav.classList.remove('col-3');
    leftNav.classList.add('col-12');
    if (content) {
      content.classList.remove('col-9');
      content.classList.add('col-0');
    }
    $('#short-list').fadeOut(300, function () {
      $('#long-list').fadeIn(300);
    });
    $('#short-list').hide();
    $('#short-list-items').hide();
    $('#long-list').show();

    localStorage.setItem("leftNavState", "expanded");
  }
  // Collapse from col-12 → col-3
  else if (leftNav.classList.contains('col-12')) {
    leftNav.classList.remove('col-12');
    leftNav.classList.add('col-3');
    if (content) {
      content.classList.remove('col-0');
      content.classList.add('col-9');
    }
    $('#long-list').fadeOut(300, function () {
      $('#short-list').fadeIn(300);
    });
    $('#short-list').show();
    $('#short-list-items').show();
    $('#long-list').hide();
    localStorage.setItem("leftNavState", "collapsed");
  }
  const state = localStorage.getItem("leftNavState");
}


/*side panel short list to long list*/

/*table resize*/
document.querySelectorAll('th.resizable').forEach(function (th) {
  const resizer = th.querySelector('.resizer');
  if (!resizer) return;
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

if (table && contextMenu) {
  table.addEventListener('contextmenu', function (e) {
    e.preventDefault();

    const tr = e.target.closest('tr');
    if (!tr || !tr.parentNode.matches('tbody')) return;

    selectedRow = tr;

    contextMenu.style.top = `${e.pageY - 60}px`;
    contextMenu.style.left = `${e.pageX + 20}px`;
    contextMenu.style.display = 'block';
  });


  // Hide menu on click elsewhere
  document.addEventListener('click', function () {
    contextMenu.style.display = 'none';
  });

  // Add Row (inserts empty row at selected position — selected row shifts down)
  document.getElementById('addRow').addEventListener('click', function () {
    if (selectedRow) {
      var newRow = addEmptyMyTableRow($(selectedRow));

      // Insert the new empty row BEFORE the selected row so selected row moves down
      selectedRow.parentNode.insertBefore(newRow, selectedRow);

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

    // Recalculate serial numbers after delete
    const allRows = document.querySelectorAll('#myTable tbody tr');
    allRows.forEach((row, index) => {
      const serialInput = row.cells[0].querySelector('input');
      if (serialInput) {
        serialInput.value = index + 1;
      }
    });
  } update_totals();
});
  /*table row addrow deleterow*/
}
/*date picker*/
$(document).ready(function () {
  $(document).on('click', '#long-list > tbody > tr', function (e) {
    // prevent triggering when clicking inside a nested table
    if ($(e.target).closest('table').attr('id') !== 'long-list') {
      return;
    }

    if ($(e.target).closest('td').hasClass('no-toggle')) {
      return; // do nothing if inside excluded cells
    }

    $(this).toggleClass('expand');
  });
});

flatpickr(".date-picker", {
  dateFormat: "d/m/Y", // dd/mm/yyyy
  allowInput: true
});

flatpickr(".date-picker-no-past", {
  dateFormat: "d/m/Y", // dd/mm/yyyy
  allowInput: true,
  minDate: "today"
});


$(document).ready(function () {
  $(document).on('click', '#long-list2 > tbody > tr', function (e) {
    // prevent triggering when clicking inside a nested table
    if ($(e.target).closest('table').attr('id') !== 'long-list2') {
      return;
    }

    if ($(e.target).closest('td').hasClass('no-toggle')) {
      return; // do nothing if inside excluded cells
    }

    $(this).toggleClass('expand');
  });
});

flatpickr(".date-time-picker", {
  enableTime: true,
  dateFormat: "d/m/Y h:i K", // dd/mm/yyyy hh:mm AM/PM
  allowInput: true,          // allows typing
  time_24hr: false,          // 12-hour format with AM/PM
  minuteIncrement: 1         // finer control
});

$(document).on("click", ".truncate-text-custom", function () {
  $(this).toggleClass("expanded");
});

$(document).ready(function () {
  $("#tableSearch").on("keyup", function () {
    let value = $(this).val().toLowerCase();
    $(".data-table tbody tr").filter(function () {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });

  // ESC key to clear tableSearch input
  $("#tableSearch").on("keydown", function (e) {
    if (e.key === 'Escape' || e.keyCode === 27) {
      $(this).val('');
      $(this).trigger('keyup'); // Trigger search to show all rows
    }
  });
});

$(document).ready(function () {
  $("#tableSearchTrialBalance").on("keyup", function () {
    let value = $(this).val().toLowerCase();

    // Only filter rows that are not collapse rows
    $(".data-table tbody tr").not(".collapse").filter(function () {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
    });
  });
});


// Clear input fields on Escape — Production Ready Version
$(document).on('keydown', 'input, textarea', function (e) {
  // Only act on Escape key
  if (e.key === 'Escape' || e.keyCode === 27) {
    const $field = $(this);

    // Skip special input types (password, checkbox, radio, file, hidden)
    const type = $field.attr('type');
    if (['password', 'checkbox', 'radio', 'file', 'hidden'].includes(type)) {
      return;
    }

    // Skip read-only or disabled inputs
    if ($field.is('[readonly]') || $field.is(':disabled')) {
      return;
    }

    // Clear value only if not already empty
    if ($field.val() !== '') {
      $field.val('').trigger('input').trigger('change');
    }

    // Optional: visually indicate the clear action
    $field.addClass('input-cleared');
    setTimeout(() => $field.removeClass('input-cleared'), 200);
  }
});




$(document).ready(function () {
  // Title-case inputs having class 'capitalize-title'
  $(document).on('input', '.capitalize-title', function () {
    var val = $(this).val() || '';
    // Make everything lowercase first, then uppercase word-start letters
    val = val.toLowerCase().replace(/\b\w/g, function (c) { return c.toUpperCase(); });
    $(this).val(val);
  });
});


$(document).ready(function () {
  $("#tableSearchBill").on("keyup", function () {
    let value = $(this).val().toLowerCase();
    $(".data-table-bill tbody tr").filter(function () {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });

  // ESC key to clear tableSearch input
  $("#tableSearchBill").on("keydown", function (e) {
    if (e.key === 'Escape' || e.keyCode === 27) {
      $(this).val('');
      $(this).trigger('keyup'); // Trigger search to show all rows
    }
  });
});

/*myTable Enter-key row navigation*/
function addEmptyMyTableRow($currentRow) {
  var sourceRow = $currentRow[0];
  var newRow = sourceRow.cloneNode(true);

  // Clear all input and textarea values
  newRow.querySelectorAll('input, textarea').forEach(function (el) {
    el.value = '';
    if (el.type === 'checkbox' || el.type === 'radio') el.checked = false;
  });

  // Remove Select2-injected sibling containers cloned with the row
  newRow.querySelectorAll('.select2-container').forEach(function (el) {
    el.parentNode.removeChild(el);
  });

  // Reset every select universally: strip Select2 state, clear value
  newRow.querySelectorAll('select').forEach(function (select) {
    select.classList.remove('select2-hidden-accessible');
    select.removeAttribute('data-select2-id');
    select.removeAttribute('aria-hidden');
    select.style.display = '';
    select.selectedIndex = -1;
    select.value = '';
  });

  return newRow;
}

document.addEventListener('keydown', function (e) {
  if (e.key !== 'Enter') return;
  var target = e.target;
  if (!(target instanceof HTMLElement)) return;
  var currentRow = target.closest('tr');
  if (!currentRow || !currentRow.closest('#myTable')) return;
  if (target.matches('[data-enter-skip]')) return;

  var focusableSelector =
    'input:not([type="hidden"]):not([disabled]):not([readonly]), ' +
    'select:not([disabled]):not([readonly]), ' +
    'textarea:not([disabled]):not([readonly])';

  var $currentRow = $(currentRow);
  var $allRows = $('#myTable tbody tr');
  var $focusable = $currentRow.find('td:not(:first-child)')
    .find(focusableSelector).filter(':visible');

  if ($focusable.length === 0) return;
  var currentIndex = $focusable.index(target);
  if (currentIndex !== $focusable.length - 1) return;

  var currentRowIndex = $allRows.index($currentRow);
  var $nextRow = $allRows.eq(currentRowIndex + 1);
  if ($nextRow.length) return;

  // Add new row in capture phase — fires before any page-specific bubble handler
  var newRow = addEmptyMyTableRow($currentRow);
  $('#myTable tbody')[0].appendChild(newRow);
  document.querySelectorAll('#myTable tbody tr').forEach(function (row, index) {
    var serialInput = row.cells[0].querySelector('input');
    if (serialInput) serialInput.value = index + 1;
  });

  // Focus after all handlers have settled
  setTimeout(function () {
    var $newRow = $(newRow);
    var $newFocusable = $newRow.find('td:not(:first-child)')
      .find(focusableSelector).filter(':visible').first();
    if ($newFocusable.length) {
      $newFocusable.hasClass('select2-hidden-accessible')
        ? $newFocusable.select2('open')
        : $newFocusable.focus();
    }
  }, 0);
}, true);

$(document).on('keydown', '#myTable input, #myTable textarea', function (e) {
  if (e.key !== 'Enter') return;

  // Let more specific handlers take precedence.
  if (e.isDefaultPrevented()) return;
  if ($(this).is('[data-enter-skip]')) return;

  e.preventDefault();

  var $currentRow = $(this).closest('tr');
  var $allRows = $('#myTable tbody tr');
  var focusableSelector =
    'input:not([type="hidden"]):not([disabled]):not([readonly]), ' +
    'select:not([disabled]):not([readonly]), ' +
    'textarea:not([disabled]):not([readonly])';

  // Exclude first column (serial number) from navigation
  var $focusable = $currentRow.find('td:not(:first-child)')
    .find(focusableSelector).filter(':visible');

  var currentIndex = $focusable.index(this);

  if (currentIndex === -1) {
    // Currently in the first (serial) column — jump to first navigable element in same row
    var $first = $focusable.first();
    if ($first.length) {
      $first.hasClass('select2-hidden-accessible') ? $first.select2('open') : $first.focus();
    }
  } else if (currentIndex < $focusable.length - 1) {
    // Move to next focusable element in the same row
    var $next = $focusable.eq(currentIndex + 1);
    $next.hasClass('select2-hidden-accessible') ? $next.select2('open') : $next.focus();
  } else {
    // Move to first focusable element (skipping serial col) in the next row
    // (capture phase already added the row if this was the very last row)
    var currentRowIndex = $allRows.index($currentRow);
    var $nextRow = $allRows.eq(currentRowIndex + 1);
    if ($nextRow.length) {
      var $nextFocusable = $nextRow.find('td:not(:first-child)')
        .find(focusableSelector).filter(':visible').first();
      if ($nextFocusable.length) {
        $nextFocusable.hasClass('select2-hidden-accessible')
          ? $nextFocusable.select2('open')
          : $nextFocusable.focus();
      }
    }
  }
});
/*myTable Enter-key row navigation*/


