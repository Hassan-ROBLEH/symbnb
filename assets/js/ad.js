$('#add-image').click(function() {
    // 1 étape : je récupére le numéro des futurs champs que je vais créer
    //const index = $('#ad_images div.form-group').length;
    const index = +$('#widgets-counter').val();

    //console.log(index);

    // 2 étape : je récupère le prototype des entrées
    const tmpl = $('#ad_images').data('prototype').replace(/__name__/g, index)

    //console.log(tmpl);
    // J'injecte ce code au sein de la div
    $('#ad_images').append(tmpl);

    $('#widgets-counter').val(index + 1);

    // Je gère le bouton supprimer
    handleDeleteButtons();
});

function handleDeleteButtons() {
    $('button[data-action="delete"]').click(function() {
        const target = this.dataset.target;
        //console.log(target);
        $(target).remove();

    });
}

function updateCounter() {
    const count = +$('#ad_images div.form-group').length;
    $('#widgets-counter').val(count);
}

updateCounter();

handleDeleteButtons();