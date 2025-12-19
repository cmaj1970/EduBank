<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\School $school
 */
?>
<div class="schools form columns content" style="max-width: 800px; margin: 2rem auto;">
    <div style="background: white; padding: 2rem; border-radius: 5px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <?= $this->Form->create($school, ['id' => 'registerform']) ?>
        <fieldset>
            <legend><?= __('Schuldaten') ?></legend>

            <div class="input text required">
                <label for="name">Schulname *</label>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span style="font-weight: bold; font-size: 1.1rem; color: var(--color-primary-dark);">PTS</span>
                    <?= $this->Form->text('name', [
                        'id' => 'name',
                        'placeholder' => 'z.B. Musterschule',
                        'required' => true,
                        'style' => 'flex: 1;'
                    ]) ?>
                </div>
                <small style="color: var(--color-text); display: block; margin-top: 0.25rem;">
                    Bitte nur den Schulnamen ohne "PTS" eingeben (z.B. "Feldbach" statt "PTS Feldbach")
                </small>
            </div>

            <div class="input text" style="margin-top: 1rem;">
                <label for="kurzname-display">Kurzname (automatisch generiert)</label>
                <input type="text" id="kurzname-display" readonly disabled style="background: #f5f5f5; cursor: not-allowed;" placeholder="Wird automatisch aus dem Schulnamen erstellt">
                <?= $this->Form->hidden('kurzname', ['id' => 'kurzname']) ?>
                <small style="color: var(--color-text); display: block; margin-top: 0.25rem;">
                    Der Kurzname wird automatisch aus dem Schulnamen generiert und für Benutzernamen verwendet (z.B. admin-ptsfeldbach)
                </small>
            </div>
        </fieldset>



        <div style="text-align: center; margin-top: 2rem;">
            <?= $this->Form->button(__('Speichern'), [
                'class' => 'button',
                'style' => 'font-size: 1.1rem; padding: 0.8rem 2rem;'
            ]) ?>
        </div>

        <?= $this->Form->end() ?>


    </div>
</div>

<script>
$(document).ready(function() {
    // Auto-generate short name from school name
    function generateKurzname(name) {
        var kurzname = 'pts' + name.toLowerCase()
            .replace(/ä/g, 'ae')
            .replace(/ö/g, 'oe')
            .replace(/ü/g, 'ue')
            .replace(/ß/g, 'ss')
            .replace(/[^a-z0-9]/g, ''); // Remove all special characters
        return kurzname;
    }

    // On input in school name field
    $('#name').on('input', function() {
        var schoolName = $(this).val();
        var kurzname = generateKurzname(schoolName);

        // Update display field
        $('#kurzname-display').val(kurzname);
        // Update hidden field (will be submitted)
        $('#kurzname').val(kurzname);
    });

    // Ensure short name is set before submit
    $('form#registerform').on('submit', function(e) {
        var schoolName = $('#name').val();
        if (schoolName && !$('#kurzname').val()) {
            // Generate short name if not done yet
            var kurzname = generateKurzname(schoolName);
            $('#kurzname').val(kurzname);
        }

        // Check if short name is now set
        if (!$('#kurzname').val() || $('#kurzname').val().length < 3) {
            e.preventDefault();
            alert('Bitte geben Sie einen Schulnamen ein (mindestens 3 Zeichen).');
            return false;
        }
    });

    // Form validation
    $("form#registerform").validate({
        lang: 'de',
        rules: {
            name: {
                required: true,
                minlength: 3
            }
        },
        messages: {
            name: {
                required: "Bitte geben Sie einen Schulnamen ein.",
                minlength: "Der Schulname muss mindestens 3 Zeichen lang sein."
            }
        }
    });
});
</script>
