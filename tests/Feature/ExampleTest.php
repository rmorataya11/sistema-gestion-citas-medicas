<?php

test('the home url redirects to the filament admin', function () {
    $this->get('/')
        ->assertRedirect('/admin');
});
