jQuery(document).ready(function($){
    // Zdefiniuj zbiór elementów, które mają być skalowane – można rozszerzyć według potrzeb.
    var resizableElements = $("p, span, a, li, h1, h2, h3, h4, h5, h6");

    // Inicjalizacja: dla każdego elementu zapisz oryginalne rozmiary (font-size i line-height) w atrybutach data-
    resizableElements.each(function(){
        var $el = $(this);
        if (!$el.data("original-font-size")) {
            var fontSize = parseFloat($el.css("font-size"));
            if(isNaN(fontSize) || fontSize <= 0) {
                fontSize = 16;  // wartość domyślna
            }
            $el.data("original-font-size", fontSize);
            
            var lineHeight = parseFloat($el.css("line-height"));
            // Jeśli line-height jest "normal" lub nie da się go sparsować, ustal wartość jako fontSize * 1.6
            if(isNaN(lineHeight) || $el.css("line-height") === "normal"){
                lineHeight = fontSize * 1.6;
            }
            $el.data("original-line-height", lineHeight);
            // Zachowaj stosunek line-height do font-size
            $el.data("line-height-ratio", lineHeight / fontSize);
        }
    });
    
    // Pobierz ustawienia z PHP (przyrost w pikselach, minimum i maksimum – odnoszą się do wartości bezwzględnych)
    var textStep = (typeof accessibility_config !== 'undefined' && accessibility_config.textStep) ? parseFloat(accessibility_config.textStep) : 1;
    var minTextSize = (typeof accessibility_config !== 'undefined' && accessibility_config.minTextSize) ? parseFloat(accessibility_config.minTextSize) : 10;
    var maxTextSize = (typeof accessibility_config !== 'undefined' && accessibility_config.maxTextSize) ? parseFloat(accessibility_config.maxTextSize) : 40;
    console.log("Text step:", textStep, "Min text size:", minTextSize, "Max text size:", maxTextSize);
    
    // Globalna zmienna określająca o ile (w pikselach) zwiększyć/zmniejszyć rozmiar,
    // przy czym zmiany liczymy jako sumaryczny przyrost dodany do oryginalnego rozmiaru.
    var globalIncrement = 0;
    
    // Funkcja aktualizująca rozmiary wszystkich elementów na podstawie wartości globalIncrement.
    function updateResizableElements() {
        resizableElements.each(function(){
            var $el = $(this);
            var originalFont = $el.data("original-font-size");
            var ratio = $el.data("line-height-ratio") || 1.6;
            var newFontSize = originalFont + globalIncrement;
            // Zabezpieczenia: nie pozwalamy, by nowy rozmiar był mniejszy niż ustawiony globalny limit lub większy niż max
            if(newFontSize < minTextSize) {
                newFontSize = minTextSize;
            }
            if(newFontSize > maxTextSize) {
                newFontSize = maxTextSize;
            }
            $el.css("font-size", newFontSize + "px");
            $el.css("line-height", (newFontSize * ratio) + "px");
        });
    }
    
    // Przycisk "Increase Text" – zwiększa globalny przyrost i aktualizuje rozmiary
    $('#accessibility-btn-increase').click(function(){
        globalIncrement += textStep;
        updateResizableElements();
    });
    
    // Przycisk "Decrease Text" – zmniejsza globalny przyrost i aktualizuje rozmiary
    $('#accessibility-btn-decrease').click(function(){
        globalIncrement -= textStep;
        updateResizableElements();
    });
    
    // Przycisk "Reset" – przywraca oryginalne rozmiary oraz zeruje globalny przyrost
    $('#accessibility-btn-reset').click(function(){
        globalIncrement = 0;
        resizableElements.each(function(){
            var $el = $(this);
            var originalFont = $el.data("original-font-size");
            var originalLH = $el.data("original-line-height");
            $el.css("font-size", originalFont + "px");
            $el.css("line-height", originalLH + "px");
        });
        $("body").removeClass("accessibility-high-contrast");
    });
    
    // Przycisk "Toggle Contrast" – pozostaje bez zmian
    $('#accessibility-btn-contrast').click(function(){
        $("body").toggleClass("accessibility-high-contrast");
    });
});
