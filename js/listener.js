function TheGame(url, urlGame, login) {
    this.url = url;
    this.login = login;
    this.timeout = 360;
    this.lasCountUsers = 0;
    this.urlGame = urlGame;
    var self = this;

    this.init = function () {
        self.connection();
    };

    this.connection = function () {
        $.ajax({
            type: "GET",
            url: self.url,
            dataType: "json",
            data: {'lasCountUsers': self.lasCountUsers},
            timeout: self.timeout * 1000,
            success: self.parseData,
            error: function () {
                // something wrong. but setInterval will set up connection automatically
                setTimeout(self.connection, 1000);
            }
        });
    };

    this.parseData = function (result) {
        self.showUsers(result[0]);
        self.showLobbies(result[1]);
        setTimeout(self.connection, 1000);
    };

    this.showUsers = function (result) {
        var users = '',
            res = result[0];
        for (var i in res) {
            users += '<li>' + res[i][1] + '</li>';
        }
        $('ul#users').html(users);

        self.lasCountUsers = result[0][1];
    };

    this.showLobbies = function (result) {
        var lobbies = '',
            res = result,
            link = '';
        for (var i in res) {
            if (res[i][1] == self.login) {
                link = "<a href='" + self.urlGame + "?id=" + res[i][0] + "'>This is your lobby<a>";
            } else {
                if (res[i][2] == 1) {
                    link = "<a href='" + self.urlGame + "?id=" + res[i][0] + "'>Let's PLAY!<a>";
                } else {
                    link = 'Room is full';
                }
            }
            lobbies += "<tr><td>" + res[i][3] + '</td><td>' + res[i][2] + "</td><td>"
                + res[i][1] + '</td><td>' + link + '</td><td>' + res[i][4] + "s</td></tr>";
        }
        $('table#lobbies tbody').html(lobbies);
    };
}
