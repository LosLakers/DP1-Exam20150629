<?php
if (isset($error)) {
    ?>
    <div class="<?=get_message_type($error)?>">
        <h3 class="error-text">"<?= get_message($error) ?>"</h3>
        <button class="error-message" type="button">Close</button>
    </div>
<?php
}
?>