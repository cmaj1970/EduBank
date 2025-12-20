<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\School $school
 */
?>

<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-building me-2"></i><?= __('Neue Schule anlegen') ?></h5>
            </div>
            <div class="card-body">
                <?= $this->Form->create($school, ['id' => 'registerform']) ?>

                <div class="mb-3">
                    <label for="name" class="form-label"><?= __('Schulname') ?> <span class="text-danger">*</span></label>
                    <?= $this->Form->text('name', [
                        'class' => 'form-control',
                        'id' => 'name',
                        'placeholder' => 'z.B. Musterschule',
                        'required' => true
                    ]) ?>
                </div>

                <div class="mb-3">
                    <label for="kurzname-display" class="form-label"><?= __('Kurzname') ?></label>
                    <input type="text" id="kurzname-display" class="form-control bg-light" readonly placeholder="Wird automatisch generiert">
                    <?= $this->Form->hidden('kurzname', ['id' => 'kurzname']) ?>
                    <div class="form-text">
                        Der Kurzname wird automatisch aus dem Schulnamen generiert und für Benutzernamen verwendet (z.B. admin-musterschule)
                    </div>
                </div>

                <div class="d-flex gap-2 justify-content-end">
                    <?= $this->Html->link(__('Abbrechen'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
                    <?= $this->Form->button(__('Speichern'), ['class' => 'btn btn-primary']) ?>
                </div>

                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>

<?php $this->start('script'); ?>
<script>
$(document).ready(function() {
    // Kurzname automatisch aus Schulname generieren
    function generateKurzname(name) {
        var kurzname = name.toLowerCase()
            .replace(/ä/g, 'ae')
            .replace(/ö/g, 'oe')
            .replace(/ü/g, 'ue')
            .replace(/ß/g, 'ss')
            .replace(/[^a-z0-9]/g, '');
        return kurzname;
    }

    // Bei Eingabe im Schulnamen-Feld
    $('#name').on('input', function() {
        var schoolName = $(this).val();
        var kurzname = generateKurzname(schoolName);
        $('#kurzname-display').val(kurzname);
        $('#kurzname').val(kurzname);
    });

    // Vor dem Absenden sicherstellen dass Kurzname gesetzt ist
    $('form#registerform').on('submit', function(e) {
        var schoolName = $('#name').val();
        if (schoolName && !$('#kurzname').val()) {
            var kurzname = generateKurzname(schoolName);
            $('#kurzname').val(kurzname);
        }

        if (!$('#kurzname').val() || $('#kurzname').val().length < 3) {
            e.preventDefault();
            alert('Bitte geben Sie einen Schulnamen ein (mindestens 3 Zeichen).');
            return false;
        }
    });

    // Formularvalidierung
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
<?php $this->end(); ?>
