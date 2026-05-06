(function ($) {
"use strict";
// TOP Menu Sticky
$(window).on('scroll', function () {
	var scroll = $(window).scrollTop();
	if (scroll < 400) {
		$("#sticky-header").removeClass("sticky");
	} else {
		$("#sticky-header").addClass("sticky");
	}
});

// TOP Menu Sticky(blog-page)
$(window).on('scroll', function () {
	var scroll = $(window).scrollTop();
	if (scroll < 400) {
		$("#sticky-header").removeClass("sticky");
	} else {
		$("#sticky-header").addClass("sticky");
	}
});


// hide_menu
$(".humbugar").on('click', function(){
  $(".menu_text").toggleClass("hide");
  $(".infix_sidebar").toggleClass("min_width");
  $(".big_logo").toggleClass("d-none");
  $(".min-logo").toggleClass("d-block");
  $(".humbugar").toggleClass("active");
  $(".navbar_header").toggleClass("minus_padding");
  $(".header").toggleClass("plus_padding");
  $(".content_body").toggleClass("minus_margin");
  $(".footer_area").toggleClass("minus_margin");
});

// open menu 
// infix_sidebar 
// $(".infix_sidebar").hover(function(){
//   $(".infix_sidebar").removeClass("min_width");
// });



// dropdown
$('.sub-menu ul').hide();
$(".sub-menu a").on('click', function () {
	$(this).parent(".sub-menu").children("ul").slideToggle("100");
	$(this).find(".right").toggleClass("fa-caret-up fa-caret-down");
});

// copy hover 




// to do form
$(document).ready(function () {

  $('#list-items').html(localStorage.getItem('listItems'));
    
  $('.add-items').submit(function(event) 
  {
    event.preventDefault();

    var item = $('#todo-list-item').val();

    if(item) 
    {
      $('#list-items').append('<li><input class="checkbox" type="checkbox" id="" /><label for="checkbox"></label>' + item + '<a class="remove"><i class="ti-close"></i></a></li>');
      
      localStorage.setItem('listItems', $('#list-items').html());
      
      $('#todo-list-item').val("");
    }
    
  });

  $(document).on('change', '.checkbox', function() 
  {
    if($(this).attr('checked')) 
    {
      $(this).removeAttr('checked');
    } 
    else 
    {
      $(this).attr('checked', 'checked');
    }

    $(this).parent().toggleClass('completed');
    
    localStorage.setItem('listItems', $('#list-items').html());
  });

  $(document).on('click', '.remove', function() 
  {
    $(this).parent().remove();
    
    localStorage.setItem('listItems', $('#list-items').html());
  });

});


// select 
$(document).ready(function() {
  $('select').niceSelect();
});

// modal_pop_up 
$('.popup-with-form').magnificPopup({
  type: 'inline',
  preloader: false,
  focus: '#name',

  // When elemened is focused, some mobile browsers in some cases zoom in
  // It looks not nice, so we disable it:
  callbacks: {
    beforeOpen: function() {
      if($(window).width() < 700) {
        this.st.focus = false;
      } else {
        this.st.focus = '#name';
      }
    }
  }
});
  

$(".popup_copy").on('click', function(){
  $(".popup_copyOpen").addClass("open");
})

$(".close_hover").on('click', function(){
  $(".copy_task_hover").removeClass("open");

})
// move_open 
$(".Move_Open").on('click', function(){
  $(".move_open").addClass("open");
})

// move_share
$(".generate_task").on('click', function(){
  $(".generate_task_open").addClass("open");
})

$(".close_hover").on('click', function(){
  $(".copy_task_hover").removeClass("open");

})



})(jQuery);	
