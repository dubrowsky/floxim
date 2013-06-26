var dictionary = {};
$.get('/floxim_files/js_dictionaries/js-dictionary-en.txt', function(data) {
    dictionary = eval("("+ data +")");
});