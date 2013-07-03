function fen_toggle_hi_node(collapser) {
	collapser = collapser instanceof HTMLElement ? collapser : this;
	var node = collapser.parentNode.nextSibling;
	if (node instanceof HTMLElement) {
		$(node).toggle();
	}
}
var count_nodes = 0;
function fen_toggle_all(node) {
	$(node).find('.hi_collapser span').each( function () {
			count_nodes++;
			document.title = count_nodes;
			fen_toggle_hi_node(this);
	});
}

function fen_debug_show(id) {
	$('#'+id).toggle();
}

$(document).ready(function() {
		$('.hi_pre input').keypress( function(e) {
				if (e.keyCode != 13) {
					return;
				}
				var term = this.value;
				var w = $(this).closest('.hi_pre');
				w.find('.hi_collapse').hide();
				w.find('.data_found').removeClass('data_found');
				w.find('.hi_line').each(function() {
						if ($(this).text().indexOf(term) >= 0) {
							$(this).addClass('data_found');
							$(this).parents('.hi_collapse').show();
						}
				});
		})
})