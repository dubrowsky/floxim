$('html').on('click', '.fx_debug_collapser span', function() {
    var node = this.parentNode.nextSibling;
    $(node).toggle();
});

$('html').on('dblclick', '.fx_debug_collapser span', function() {
    var $node = $(this.parentNode.nextSibling);
    if ($node.is(':visible')) {
        $node.hide();
        $('.fx_debug_collapse', $node).hide();
    } else{
        $node.show();
        $('.fx_debug_collapse', $node).show();
    }
    return false;
});