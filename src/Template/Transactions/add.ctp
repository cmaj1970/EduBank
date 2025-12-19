<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Transaction $transaction
 */

?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li><?= $this->Html->link(__('Umsätze'), ['controller' => 'Accounts', 'action' => 'view', $account->id]) ?> </li>
        <li><?= $this->Html->link(__('Auftragshistorie'), ['controller' => 'Accounts', 'action' => 'history', $account->id]) ?> </li>
        <li><?= $this->Html->link(__('New Transaction'), ['controller' => 'Transactions', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="transactions form large-9 medium-8 columns content">
    <?= $this->Form->create($transaction, ['id' => 'addtransaction']) ?>
    <fieldset>
        <legend><?= __('Add Transaction') ?></legend>
        <div id="transactionform">
        <?php
            echo $this->Form->control('account_id', ['options' => $accounts, 'empty' => false, 'label' => 'Konto Auftraggeber']);
            echo $this->Form->control('empfaenger_name', ['required']);
            echo $this->Form->control('empfaenger_adresse');
            echo $this->Form->control('empfaenger_iban');
            echo $this->Form->control('empfaenger_bic');
            echo $this->Form->control('betrag', ['type' => 'text', 'label' => 'Betrag (max. ' . $this->Number->currency($max_betrag, 'EUR', ['useIntlCode' => true]) . ')']);
            echo $this->Form->control('datum', ['templates' => [
                    'dateWidget' => '<div class="clearfix">{{day}}{{month}}{{year}}</div>',
                ]]);
            echo $this->Form->control('zahlungszweck');
        ?>
        </div>
        <div id="taninput" style="display:none;">
		    <?= $this->Form->control('tan', ['type' => 'text',  'label' => 'TAN']); ?>
		    <?= $this->Form->button(__('Abbrechen'), ['type' => 'button', 'id' => 'cancel']) ?>
		    <?= $this->Form->button(__('Submit'), ['type' => 'button', 'id' => 'tansubmit']) ?>
        </div>
	    <?= $this->Form->button(__('mit TAN zeichnen'), ['type' => 'button', 'id' => 'requesttan']) ?>

    </fieldset>
    <?= $this->Form->end() ?>
</div>
<script>
    $(document).ready(function() {
        $("form#addtransaction").validate({
            lang: 'de'  // or whatever language option you have.
        });
    });
    $('#requesttan').click(function () {
            var $form = $( "form" );
            var $iban = $form.find('#empfaenger-iban').val();
            var $checkiban = true;
        $.ajax({
            url: '/transactions/checkiban',
            data: $iban,
            success: function (response) {
                console.log(response);
                if(response != "true") {
                    alert("Bitte die IBAN überprüfen.");
                }
            },
            error: function() {
                alert('ajax error');
            }
        }
        );
            var validator = $( "#addtransaction" ).validate({
                lang: 'de'
            });
            validator.form();
            if($checkiban == true) {
                if(validator.form()) {
                    $('#addtransaction').validate();
                    $('#taninput').show();
                    $('#requesttan').hide();
                    $('#transactionform').find(':input').prop('readonly',true);
                }
            } else {
                alert('Bitte IBAN überprüfen.');
            }
        }
    );
    $('#cancel').click(function () {
            $('#taninput').hide();
            $('#requesttan').show();
            $('#tan').val('');
            $('#transactionform').find(':input').prop('readonly',false);
        }
    );
    $('#tansubmit').click(function () {
            var tanval = $('#tan').val();
            var modulo = $('#tan').val()%7;
            if(tanval == 0 || modulo > 0 || tanval < 10000 || tanval > 99999 ) {
                alert('Ungültige TAN');
            } else {
                this.form.submit();
            }
        }
    );
    (function($, undefined) {

        "use strict";

        // When ready.
        $(function() {

            var $form = $( "form" );
            var $input = $form.find( "#betrag" );
            $input.on( "focusout", function( event ) {
                var $this = $( this );
                if($this.val() == '') {
                    $this.val = '0.00';
                }
                var input = $this.val();
                input = input.replace('.', '');
                input = input.replace(',', '.');
                input = parseFloat(input).toFixed(2);
                input = input.replace('.', ','); // replace decimal point character with ,
                input = input.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
                // Replace decimal point character with thousand separator
                // Assign target string

                $this.val( function() {
                    return input;
                } );
            } );
        });
        $(function() {
            var $form = $( "form" );
            var $checkiban = $form.find( "#empfaenger-iban" );
            $checkiban.on( "focusout", function( event ) {
                var $this = $( this );
                var input = $this.val();
                input = input.replace(' ', '');
                $this.val( function() {
                    return input;
                } );
            } );


        });
    })(jQuery);
</script>
