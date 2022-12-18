ScrollReveal().reveal('.btn_reveal .btn', {interval: 200});
ScrollReveal().reveal('.load-hidden');
$("html").click(function() {
  if(document.getElementById('div_footer_notification').classList.contains('notification_max')){
    document.getElementById('div_footer_notification').classList.replace('notification_max','notification_min');
  }
});
$('#div_footer_notification,#btn_footer_notification').click(function (e) {
    e.stopPropagation();
});
