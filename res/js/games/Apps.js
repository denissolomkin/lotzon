(function () {

    Apps = {

        audio: [],
        modes: [],
        variations: [],

        playAudio: function (key) {
            if (!$.cookie("audio-off")) {
                if ($.isArray(key)) {
                    if (Apps.audio && Apps.audio[key[0]] && (file = Apps.audio[key[0]][key[1]]))
                        $('<audio src=""></audio>').attr('src', 'res/audio/' + file).trigger("play");
                } else if (key) {
                    $('<audio src=""></audio>').attr('src', 'res/audio/' + key).trigger("play");
                }
            }
        }

    };

})();