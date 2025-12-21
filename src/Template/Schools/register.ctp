<?php
/**
 * Public school registration page
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\School $school
 */
?>

<div class="row justify-content-center py-4">
    <div class="col-lg-5 col-md-8">
        <!-- Header -->
        <div class="text-center mb-4">
            <i class="bi bi-bank2 text-primary" style="font-size: 3rem;"></i>
            <h2 class="mt-2 mb-1">EduBank</h2>
            <p class="text-muted">Schule registrieren</p>
        </div>

        <div class="card border-0 shadow">
            <div class="card-body p-4">
                <h5 class="card-title mb-4"><i class="bi bi-building me-2"></i><?= __('Neue Schule anlegen') ?></h5>

                <?= $this->Form->create($school, ['id' => 'registerform']) ?>

                <div class="mb-3">
                    <label for="name" class="form-label"><?= __('Schulname') ?> <span class="text-danger">*</span></label>
                    <?= $this->Form->text('name', [
                        'class' => 'form-control form-control-lg',
                        'id' => 'name',
                        'placeholder' => 'z.B. Testschule Musterstadt',
                        'required' => true
                    ]) ?>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label"><?= __('E-Mail-Adresse') ?> <span class="text-danger">*</span></label>
                    <?= $this->Form->email('email', [
                        'class' => 'form-control form-control-lg',
                        'id' => 'email',
                        'placeholder' => 'z.B. admin@schule.at',
                        'required' => true
                    ]) ?>
                    <div class="form-text">
                        <i class="bi bi-envelope-check me-1"></i>Die Zugangsdaten werden an diese Adresse gesendet.
                    </div>
                </div>

                <div class="mb-4">
                    <label for="kurzname-display" class="form-label"><?= __('Kurzname') ?></label>
                    <input type="text" id="kurzname-display" class="form-control bg-light" readonly placeholder="Wird automatisch generiert">
                    <?= $this->Form->hidden('kurzname', ['id' => 'kurzname']) ?>
                    <div class="form-text">
                        Wird für Benutzernamen verwendet (z.B. admin-testschule)
                    </div>
                </div>

                <div class="d-grid gap-2 mb-3">
                    <?= $this->Form->button(__('Schule registrieren'), ['class' => 'btn btn-primary btn-lg']) ?>
                </div>

                <?= $this->Form->end() ?>

                <hr>

                <div class="text-center">
                    <span class="text-muted">Bereits registriert?</span>
                    <a href="/users/login" class="ms-1">Anmelden</a>
                </div>
            </div>
        </div>

        <!-- Info Box -->
        <div class="card border-0 shadow-sm mt-4 bg-light">
            <div class="card-body p-3">
                <h6 class="card-title mb-2"><i class="bi bi-info-circle text-primary me-2"></i>So geht's weiter</h6>
                <ol class="mb-0 ps-3 small text-muted">
                    <li>Registrieren Sie Ihre Schule mit dem Formular oben</li>
                    <li>Sie erhalten Ihre Zugangsdaten per E-Mail</li>
                    <li>Melden Sie sich an und legen Sie Übungsfirmen an</li>
                    <li>Die Übungsfirmen können dann mit ihren Konten arbeiten</li>
                </ol>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="/" class="text-muted small"><i class="bi bi-arrow-left me-1"></i>Zurück zur Startseite</a>
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
            },
            email: {
                required: true,
                email: true
            }
        },
        messages: {
            name: {
                required: "Bitte geben Sie einen Schulnamen ein.",
                minlength: "Der Schulname muss mindestens 3 Zeichen lang sein."
            },
            email: {
                required: "Bitte geben Sie eine E-Mail-Adresse ein.",
                email: "Bitte geben Sie eine gültige E-Mail-Adresse ein."
            }
        }
    });
});
</script>
<?php $this->end(); ?>
