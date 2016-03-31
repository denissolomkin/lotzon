(function () {

    Apps = {

        audio: {},
        modes: {},
        variations: {},
        sample: null,

        playAudio: function (key) {
            console.error('playAudio', key);
            if (!$.cookie("audio-disabled")) {
                if (typeof key === 'object') {
                    if (Apps.audio && Apps.audio[key[0]] && (file = Apps.audio[key[0]][key[1]]))
                        $('<audio src=""></audio>').attr('src', 'tpl/audio/' + file).trigger("play");
                } else if (key) {
                    $('<audio src=""></audio>').attr('src', 'tpl/audio/' + key).trigger("play");
                }
            }
        }

    };

})();