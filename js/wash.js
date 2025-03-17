// Are we sure we should be clicking the button we just clicked?
function checkConflict(action_id)
{
    let pieces = action_id.split('_');
    console.dir(pieces);
    let format = $('#format_' + pieces[1]).val();

    // Is this vinyl and we're NOT washing it?
    if (format.includes('Vinyl') && pieces[0] == 'na') {
        $('#modal_confirm_text').html(
            'This is vinyl - are you sure you don\'t want to wash it?'
        );
        $('.confirmYes').attr('id', action_id);
        $('#confirmation_modal').modal('show');
    } else if (pieces[0] != 'na' && !(format.includes('Vinyl'))) {
        // Is this NOT vinyl but it's being washed?
        $('#modal_confirm_text').html(
            'This is not vinyl - are you sure this title was washed?'
        );
        $('.confirmYes').attr('id', action_id);
        $('#confirmation_modal').modal('show');
    } else {
        // No conflict!
        switch(pieces[0]) {
            // We're manually entering the date.
            case 'ed':
                $('.submitDate').attr('id', action_id);
                $('#date_modal').modal('show');
                break;
            // We're auto-adding either today or "n/a".
            case  'na':
            case 'today':
                post_data({'action_instance' : action_id});
        }
    }
}

// AJAX POST call
function post_data(data)
{
    $.post('post/washy.php', data, function(response) {
        if (response === '1') {
            location.reload();
        } else {
            $('#date_modal').modal('hide');
            $('#modal_error_text').html(response);
            $('#error_modal').modal('show');
        }
    });
}

// jQuery stuff
$(document).ready(function() {
    // User has manually entered the date and clicked submit.
    $('.submitDate').on('click', function() {
        let post_object = {
            'action_instance': $(this).attr('id'),
            'new_date'       : $('#wash_date').val(),
        };

        post_data(post_object);
    });

    // Handle clicking the "Yes" button on the confirmation modal
    $('.confirmYes').on('click', function() {
        let post_object = {
            'action_instance': $(this).attr('id'),
        };

        // If we specify a date that's not today, open the date modal.
        if (post_object.action_instance.includes('ed_')) {
            $('#confirmation_modal').modal('hide');
            $('.submitDate').attr('id', $(this).attr('id'));
            $('#date_modal').modal('show');
            return;
        }

        post_data(post_object);
    });

});
