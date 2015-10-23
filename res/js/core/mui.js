$(function () {

    // Multilingual User Interface
    M = {
        "i18n": function (key) {
            return key ? (Texts[key] ? Texts[key] : key) : (function (key) {
                return M.i18n(key);
            });
        },

        "eval": function (key) {
            return key ? eval(key) : (function (key) {
                return M.eval(key);
            });
        }
    };

});