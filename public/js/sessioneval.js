function ajaxifyForm(form) {
    $(':submit', form).click(function (event) {
        event.preventDefault();
        $.post(form.attr('action'), form.serialize(),
            function(data, status) {
                if(status == 'success') {
                    var newForm = $(data);
                    ajaxifyForm(newForm);
                    form.after(newForm).remove();
                    newForm.effect("highlight", {'color':'#B3EBB0'}, 1000);
                }
            }
        );
    });
}

$(function() {
	ajaxifyForm($('#evaluateform'));
});