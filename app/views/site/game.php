<?php
use \config\App;

$title = 'Главная';

?>

<script src="<?= App::$pathToRoot ?>/js/listenerGame.js"></script>
<script type="text/javascript">
    game = new GamePlay(
        '<?= App::url(['site/take-lobby', 'id' => $id]) ?>',
        '<?= $id ?>',
        '<?= App::url(['site/break', 'id' => $id]) ?>',
        '<?= App::url(['site/make-choice', 'id' => $id]) ?>',
        '<?= $_SESSION['login'] ?>',
        '<?= App::url(['site/delete-lobby', 'id' => $id]) ?>'
    );
    game.init();

    $(function () {
        $('div#ready').on('click', function () {
            $.ajax({
                type: "POST",
                url: '<?= App::url(['site/player-ready', 'id' => $id]) ?>',
                success: function (result) {
                    if (result) {
                        $('div#readyStatus').html('You\'re READY!');
                        $('div#ready').fadeOut(300);
                    }
                }
            });
        });

        $('ul#choice li').on('click', function () {
            var val = $(this).attr('id');

            $('strong#partner').html('');
            $('strong#winner').html('');

            // add time. If second user go out, sendChoice will running twice, from me and from him
            game.timeForRoundUpd = game.timeForRound;

            game.sendChoice(val);
        });
    })
</script>

<div id="container">
    <div id="plain">
        <div id="countPeople">
            Now in the room: <strong></strong> person
            <div id="ready"></div>
            <div id="readyStatus"></div>
        </div>
    </div>

    <div id="gameStat">
        <h1>Full game</h1>

        <p id="lot1">First game: <strong></strong></p>

        <p id="lot2">Second game: <strong></strong></p>

        <p id="lot3">Third game: <strong></strong></p>

        <p id="lotFull" style="font-size: 42px; color: crimson;"></p>

        <p id="closeAfter">Lobby will be closed after: <strong></strong> seconds</p>
    </div>

    <div id="plain">
        <div id="game">
            <h1>Make your choice!</h1>

            <p id="time">Time left: <strong> - </strong></p>
            <ul id="choice">
                <li id="rock"></li>
                <li id="scissors"></li>
                <li id="paper"></li>
            </ul>
        </div>

        <div id="gameLog">

            <div id="lotNumber">Round: <strong> - </strong> (from 3)</div>

            <div class="gameLog">Your choice is: <strong id="yours"></strong></div>
            <div class="gameLog">Partner choice is: <strong id="partner"></strong></div>
            <div class="gameLog">Winner is: <strong id="winner"></strong></div>

        </div>
    </div>
</div>