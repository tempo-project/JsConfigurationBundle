"use strict";

var Tempo = Tempo || { 'Configuration': {}};

Tempo.Configuration = {

    setData: function(data) {
        Tempo.Configuration.data = data;
    },
    get: function(key) {
        return Tempo.Configuration.data[key];
    }
};
