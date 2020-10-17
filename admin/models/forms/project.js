jQuery(function() {
    document.formvalidator.setHandler('title',
        function (value) {
            regex=/^[^\*]+$/;
            return regex.test(value);
        });
});