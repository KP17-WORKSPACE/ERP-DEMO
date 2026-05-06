$('main nav .toggle-nav').click(function() {
    $('main').toggleClass('show-nav');
});

let activeSubnav = null;
$('.nav-list .nav-item .sub-menu-nav').click(function() {
    activeSubnav = $(this).data().subnav;
    const rect = $(this)[0].getBoundingClientRect();
    const subnav = $(this).next('.subnav-menu');

    if(activeSubnav) {
        $(`.nav-item.show-subnav:not(:has(#${activeSubnav}))`).removeClass('show-subnav');
        $('#'+activeSubnav).parent('.nav-item').toggleClass('show-subnav');
    }

    const subnavRect = subnav[0].getBoundingClientRect();
    $(this).next('.subnav-menu').css({
        top: rect.top + subnavRect.height > window.innerHeight ? (window.innerHeight - (subnavRect.height + 10)) : rect.top
    });
});

// $(document).on('click', function(e) {
//     const hasMenu = $(e.target).parents('.nav-list');
//     if(!hasMenu.length) $(`.nav-item.show-subnav`).removeClass('show-subnav');
// });

$(document).ready(function() {
    if($('.form-control.date-picker').length) {
        $('.form-control.date-picker').datepicker({
            onSelect: function(dateText) {
                $(this).toggleClass('active', dateText);
            }
        });
    }

    if($('.progress').length) {
        $('.progress').each((index, progress) => {
            const width = progress?.dataset?.percentage || '100%';
            const barColor = progress?.dataset?.barColor || '#000';
            const progressBar = document.createElement('div');
            progressBar.className = 'progress-bar';
            progress.appendChild(progressBar);
            setTimeout(() => {
                progressBar.style.width = width;
                progressBar.style.backgroundColor = barColor;
            }, 0);
        });
    }
});

$(document).on('click', '.alert-dismissible button[data-dismiss="alert"]', function() {
    const alert = $(this).parent('.alert-dismissible[role="alert"]');
    const toast = $(this).parent('.alert-dismissible[role="toast"]');
    alert.removeClass('show');
    toast.removeClass('show');
    setTimeout(() => {
        alert.remove();
    }, 300);
});

$(document).on('click', '.toast-button', function() {
    const toast = this?.dataset?.toast;
    $(toast).toast('show');
});

class CustomMultiSelect {
    constructor(container=null, placeholder="Select", onChange=()=>{}) {
        this.msc = container;
        this.placeholder = placeholder;
        this.selectedItems = [];
        this.onChange = onChange;

        this.msc && this.initUI();
    }

    initUI() {
        this.data = Array.from(this.msc.options).map(option => ({ label: option.textContent, value: option.value })) || [];
        if(this.data.length) {
            this.renderUI();
        }
    }

    replaceContainer() {
        const newDiv = document.createElement("div");
        newDiv.className = this.msc.className + ' msc-wrap';
        newDiv.id = this.msc.id + "_wrap";

        const html = `<input type="hidden" class="msc-value" id="${this.msc.id}" />
        <div class="msc-container">
            <div class="msc-header">
                <div class="msc-selected-items">
                    <div class="msc-placeholder">${this.placeholder}</div>
                    <div class="msc-selected-dummy"></div>
                </div>
            </div>
            <div class="msc-dropdown">
                <div class="msc-body">${this.renderMSCBody()}</div>
            </div>                    
        </div>`;

        newDiv.innerHTML = html;

        this.msc.parentNode.replaceChild(newDiv, this.msc);
        this.msc = newDiv;
        
        this.handleCheckboxClick();
        this.handleDropdownToggle();
    }

    renderMSCBody() {
        const html = this.data.map((option, optionIndex) => {
            const itemId = `${this.msc.id}_item_${optionIndex+1}`;
            return `<div class="msc-item" data-value="${option.value}">
                <input id="${itemId}" type="checkbox" class="msc-item-input" data-label="${option.label}" value="${option.value}" />
                <label for="${itemId}" class="msc-item-label">${option.label}</label>
            </div>`;
        });

        return html.join('');
    }

    renderUI() {
        this.replaceContainer();
    }

    handleCheckboxClick() {
        Array.from(this.msc.querySelectorAll('input[type="checkbox"]')).forEach(input => {
            input.addEventListener('change', (e) => {
                const target = e.target;
                const existing = this.selectedItems.find(i => i.value === target.value);
                if(existing) {
                    this.selectedItems = this.selectedItems.filter(i => i.value !== target.value);
                    target.checked = false;
                } else {
                    this.selectedItems.push({ label: target.dataset.label, value: target.value });
                    target.checked = true;
                }
                this.onChange(this.selectedItems);
                this.renderSelected();
                this.handleDeleteItem();
            });
        });
    }

    renderSelected() {
        const selectedContainer = this.msc.querySelector('.msc-selected-items');
        const mscValue = this.msc.querySelector('.msc-value');
        const selectedItems = this.selectedItems.map(item => {
            return `<div class="msc-selected-item" data-label="${item.label}" data-value="${item.value}">
                <div class="msc-selected-item-label">${item.label}</div>
                <div class="msc-selected-item-close"></div>
            </div>`;
        });
        selectedContainer.innerHTML = this.selectedItems.length ? selectedItems.join('') : `<div class="msc-placeholder">${this.placeholder}</div>` + '<div class="msc-selected-dummy"></div>';
        mscValue.value = JSON.stringify(this.selectedItems);
    }

    handleDropdownToggle() {
        const header = this.msc.querySelector('.msc-container .msc-header');
        header.addEventListener('click', (e) => {
            e.stopPropagation();
            this.msc.classList.toggle('show');
        });
    }

    handleDeleteItem() {
        Array.from(this.msc.querySelectorAll('.msc-selected-item-close')).forEach(close => {
            close.addEventListener('click', (e) => {
                e.stopPropagation();
                const value = close.parentElement.dataset.value;
                const input = this.msc.querySelector(`.msc-item-input[value="${value}"]`);
                const changeEvent = new Event('change');
                input.dispatchEvent(changeEvent);
            });
        });
    }
}
