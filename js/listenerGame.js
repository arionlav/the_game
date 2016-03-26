function GamePlay(url, id, homeUrl, urlChoice, login, deleteLobbyUrl) {
    this.url = url;
    this.id = id;
    this.homeUrl = homeUrl;
    this.urlChoice = urlChoice;
    this.deleteLobbyUrl = deleteLobbyUrl;
    this.timeout = 360;
    this.time = 30;
    this.timeForRound = null;
    this.login = login;
    this.wasAnswer = 0;
    this.closeAfter = 10;
    var self = this;
    this.numberSecondsProp = self.closeAfter;
    this.timeForRoundUpd = self.timeForRound;
    this.lot = 0;

    this.init = function () {
        self.connection();
    };

    this.connection = function () {
        $.ajax({
            type: "GET",
            url: self.url,
            dataType: "json",
            timeout: self.timeout * 1000,
            success: self.parseData,
            error: function () {
                // something wrong. but setInterval will set up connection automatically
                setTimeout(self.connection, 1000);
            }
        });
    };

    this.parseData = function (result) {
        if (result == 0) {
            window.location = self.homeUrl;
            return;
        }

        self.showCountPeople(result[0]);

        if (self.timeForRound == null) {
            self.timeForRound = result[0][8];
            self.timeForRoundUpd = result[0][8];
        }

        setTimeout(self.connection, 1000);
    };

    this.showCountPeople = function (result) {
        if (result[2] == 1) {
            $('div#countPeople strong').html('1');
        } else if (result[2] == 2) {
            if (self.time) {
                $('div#countPeople strong').html('2');
                $('div#ready').html('Are you ready? <strong id="ready">' + self.time + '</strong>');
                $('div#lotNumber strong').html(self.lot + 1);

                if (result[4] != 0 && result[5] != 0) {
                    // users accept on the game
                    if (!self.wasAnswer) {
                        $('div#game').css({
                            'visibility': 'visible'
                        });
                    }
                    $('div#countPeople').hide();

                    self.timeForOneRound();

                    self.gameLog(result[1], result[3], result[6]); // logins and game array
                } else {
                    setInterval(self.timer(), 1000);
                }
            } else {
                window.location = self.homeUrl;
            }
        }
    };

    this.timeForOneRound = function () {
        if (self.timeForRoundUpd) {
            $('p#time strong').html(self.timeForRoundUpd);
            self.timeForRoundUpd--;
        } else {
            self.sendChoice('empty');
        }
    };

    this.timer = function () {
        self.time--;
    };

    this.sendChoice = function (val) {
        $.ajax({
            type: "GET",
            url: self.urlChoice + '&val=' + val + '&lot=' + self.lot,
            success: function () {
                if (val == 'empty') {
                    val = 'No choice :(';
                    self.wasAnswer = 0;
                } else {
                    self.wasAnswer = 1;
                }

                $('strong#yours').html(val);
                $('div#game').css({
                    'visibility': 'hidden'
                });
            }
        });
    };

    this.gameLog = function (oneLogin, twoLogin, result) {
        if (typeof result[self.lot] != 'undefined') {
            if (
                typeof result[self.lot][1] != 'undefined'
                && typeof result[self.lot][2] != 'undefined'
                && typeof result[self.lot][3] != 'undefined'
            ) {
                // we have all answers
                self.timeForRoundUpd = self.timeForRound;

                var div = $('div#gameLog strong#partner'),
                    divWin = $('strong#winner'),
                    lotNumber = $('p#lot' + (self.lot + 1) + ' strong');

                if (self.login == oneLogin) {
                    div.html(result[self.lot][2]);
                } else if (self.login == twoLogin) {
                    div.html(result[self.lot][1]);
                }

                if (result[self.lot][3] == 1) {
                    divWin.html(oneLogin);
                    lotNumber.html(oneLogin);
                } else if (result[self.lot][3] == 2) {
                    divWin.html(twoLogin);
                    lotNumber.html(twoLogin);
                } else {
                    divWin.html('tie');
                    lotNumber.html('tie');
                }
                self.wasAnswer = 0;

                if (self.lot < 2) {
                    self.lot++;
                } else {
                    self.endGame(oneLogin, twoLogin, result);
                }
            }
        }
    };

    this.endGame = function (oneLogin, twoLogin, result) {
        $('div#plain').fadeOut(300);

        var win1 = 0,
            win2 = 0,
            winner;

        for (var i in result) {
            if (result[i][3] == 1) {
                win1++;
            } else if (result[i][3] == 2) {
                win2++;
            }
        }

        if (win1 > win2) {
            winner = oneLogin;
        } else if (win1 < win2) {
            winner = twoLogin;
        } else {
            winner = 'TIE'
        }

        $('p#lotFull').html(winner);

        $('p#closeAfter').show();
        self.numberSeconds();

        setTimeout(self.deleteLobby, self.closeAfter * 1000);
    };

    this.deleteLobby = function () {
        $.ajax({
            type: "POST",
            url: self.deleteLobbyUrl
        });
    };

    this.numberSeconds = function () {
        $('p#closeAfter strong').html(self.numberSecondsProp);
        self.numberSecondsProp--;
    }
}