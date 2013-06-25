var dictionary = {};
$.get('/floxim_files/js-dictionary-en.txt', function(data) {
    dictionary = eval("("+ data +")");
});