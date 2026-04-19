<?php

it('landing page hubungi admin links to whatsapp', function () {
    visit('/')
        ->assertAttributeContains('a:has-text("Hubungi Admin")', 'href', 'wa.me');
});

it('booking page loads without smoke issues in a real browser', function () {
    visit('/booking')->assertSee('Pilih jenis paket');
});

it('booking success page loads without smoke issues in a real browser', function () {
    visit('/booking/success')->assertNoSmoke();
});
