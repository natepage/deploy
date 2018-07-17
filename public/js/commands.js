$('.terminal-input').click(function () {
    var output = $('#terminal-output-' + $(this).data('output'));

    output.is(':visible') ? output.hide() : output.show();
});