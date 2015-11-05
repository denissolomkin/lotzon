(function () {


    // Multilingual User Interface
    M = {

        "texts": null,
        "init": function(texts) {
            this.texts = texts;
        },

        "i18n": function (key) {

            key = key && key.replace(/\/|=/g, '-');
            return key
                ? (this.texts[Player.lang] && this.texts[Player.lang][key] ? this.texts[Player.lang][key] : key)
                : (function (key) { return M.i18n(key); });
        }

    };

})();