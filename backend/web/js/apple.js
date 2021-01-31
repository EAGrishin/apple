$('body').on('click', '.create-apple', function (e) {
   e.preventDefault();
   $.get('/site/create-apple', function (resp) {
      if (resp.success) {
         $('#apples').replaceWith(resp.html);
      } else {
         alert(resp.error);
      }
   });
});

$('body').on('click', '.eat-apple', function () {
   const $this = $(this);
   let apple = $this.parents('.apple-card')
   $.ajax({
      url: "/site/eat-apple",
      dataType: "json",
      data: {
         id: apple.data('id'),
         percent: $this.data('percent')
      },
      error: function (resp) {
         alert(resp.statusText + '(#' + resp.status  + ')');
      },
      success: function (resp) {
         if (resp.success) {
            apple.find('.progress').replaceWith(resp.html);
         } else if (resp.delete) {
            apple.remove();
         } else {
            alert(resp.error);
         }
      }
   });
});

$('body').on('click', '.fall-apple', function () {
   const $this = $(this);
   let apple = $this.parents('.apple-card');
   $.ajax({
      url: "/site/fall-apple",
      dataType: "json",
      data: {
         id: apple.data('id')
      },
      error: function (resp) {
         alert(resp.statusText + '(#' + resp.status  + ')');
      },
      success: function (resp) {
         if (resp.success) {
            apple.find('.apple-state').text(resp.state);
         } else {
            alert(resp.error);
         }
      }
   });
});

setInterval(function() {
   $.get('/site/rotten-apple', function (resp) {
      if (resp.success) {
         $.each(resp.ids, function(i, id) {
            let apple = $('#apples').children('[data-id="'+id+'"]')
            apple.find('.apple-state').text(resp.state);
            apple.find('.circle').css('color', '#' + resp.color)
         });
      }
   });
}, 1000 * 60 * 5);