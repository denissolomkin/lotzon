(function () {

    Apps = {

        audio: [],
        modes: [],
        variations: [],
        sample: null,

        playAudio: function (key) {
            if (!$.cookie("audio-off")) {
                if ($.isArray(key)) {
                    if (Apps.audio && Apps.audio[key[0]] && (file = Apps.audio[key[0]][key[1]]))
                        $('<audio src=""></audio>').attr('src', 'tpl/audio/' + file).trigger("play");
                } else if (key) {
                    $('<audio src=""></audio>').attr('src', 'tpl/audio/' + key).trigger("play");
                }
            }
        }

    };

})();