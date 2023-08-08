# Esporta in PDF - Plugin WordPress

Questo plugin WordPress estende le funzionalità dell'editor di WordPress aggiungendo un'opzione di esportazione in PDF per un gruppo di prodotti. Con questa opzione, è possibile selezionare più prodotti e generare un catalogo in formato PDF con informazioni personalizzate come il titolo, la descrizione, il prezzo e l'immagine per ciascun prodotto.

## Installazione

1. Copiare all'interno del function child theme.

## Utilizzo

1. Vai alla sezione "Prodotti" nel pannello di amministrazione di WordPress.
2. Seleziona i prodotti desiderati che desideri includere nel catalogo PDF.
3. Nel menu "Azioni di gruppo", seleziona "Esporta in PDF".
4. Verrà visualizzata una finestra di dialogo con le opzioni per personalizzare il catalogo PDF.
5. Inserisci un titolo per il catalogo.
6. Utilizza l'editor visuale per creare una descrizione personalizzata per ogni prodotto.
7. Specifica un prezzo personalizzato come percentuale di aggiunta o sottrazione dal prezzo originale.
8. Carica un'immagine rappresentativa per il catalogo.
9. Fai clic sul pulsante "Genera PDF" per avviare il processo di generazione del PDF.
10. Verrà generato un file PDF contenente il catalogo dei prodotti selezionati con le personalizzazioni specificate.
11. Il file PDF verrà scaricato automaticamente sul tuo dispositivo.

## Dipendenze

Il plugin fa uso delle seguenti dipendenze:

- [MPDF](https://github.com/mpdf/mpdf): Una libreria PHP per la generazione di file PDF.

Prima di installare il plugin, assicurati di avere installato [Composer](https://getcomposer.org/) sul tuo server. Quindi, esegui il seguente comando tramite terminale cPanel per installare MPDF:

`composer require mpdf/mpdf`

Specifica la cartella in cui desideri installare MPDF prima di eseguire il comando tramite `cd`.

## Personalizzazione

Se desideri personalizzare ulteriormente il plugin, puoi modificare il codice sorgente direttamente. Assicurati di avere una buona comprensione della programmazione PHP e WordPress prima di apportare modifiche.

## Contributi

Se desideri contribuire allo sviluppo di questo plugin, puoi inviare pull request su GitHub. Siamo aperti a miglioramenti, correzioni di bug e nuove funzionalità.

## Licenza

Il plugin è distribuito con licenza MIT. Consulta il file [LICENSE](LICENSE) per ulteriori informazioni sulla licenza.
