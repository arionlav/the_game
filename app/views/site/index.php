<?php
use \config\App;

$title = 'Главная';

?>
<script src="<?= App::$pathToRoot ?>/js/listener.js"></script>
<script type="text/javascript">
    /*

     function sub() {
     $.ajax({
     type: 'post',
     url: "<?//= App::url(['site/result']) ?>",
     success: function (res) {
     var users = '';
     for (var i in res) {
     users += res[i] + '<br>';
     }
     $('div#users').html(users);
     setTimeout(sub, 1000);
     },
     dataType: 'json'
     })
     }

     function getLobbies() {
     $.ajax({
     type: 'post',
     url: "<?//= App::url(['site/get-lobbies']) ?>",
     success: function (res) {
     var lobbies = '';
     for (var i in res) {
     lobbies += "<p><a href='" + "<?//= App::url(['site/game']) ?>?id=" + res[i][0] + "'>"
     + res[i][0] + "<a> - " + res[i][2] + " - " + res[i][1] + "</p>";
     }
     $('div#lobbies').html(lobbies);
     setTimeout(getLobbies, 1000);
     },
     dataType: 'json'
     })
     }

     $(function() {
     sub();
     getLobbies();
     });


     */

    $game = new TheGame(
        '<?= App::url(['site/result']) ?>',
        '<?= App::url(['site/game']) ?>',
        '<?= $_SESSION['login'] ?>'
    );
    $game.init();
</script>

<div id="container">
    <div id="usersContainer">
        <h2>Users list</h2>
        <ul id="users"></ul>
    </div>

    <div id="lobbiesContainer">
        <h2>Lobby list</h2>
        <table id="lobbies">
            <thead>
            <tr>
                <th>Lobby name</th>
                <th>Participants now</th>
                <th>Owner</th>
                <th>Link</th>
                <th>Time for round</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <div id="addLobbyContainer">
        <h2>Add Lobby</h2>
        <form action="<?= App::url(['site/add-lobby']) ?>" method="post">
            <label for="lobbyName">Enter lobby name or it will be made automatically:</label>
            <input type="text" name="lobbyName" id="lobbyName" value="">

            <label for="roundLength">Round length, s:</label>
            <select name="roundLength" id="roundLength">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="15">15</option>
                <option value="30" selected>30</option>
                <option value="30">60</option>
            </select>

            <input type="submit" value="Add Lobby">
        </form>
    </div>
</div>