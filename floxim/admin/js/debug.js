$('html').on('click', '.fx_debug_collapser span', function() {
    var node = this.parentNode.nextSibling;
    $(node).toggle();
});