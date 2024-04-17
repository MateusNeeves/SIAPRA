function printPdf(name) {   
    $(".collapse").collapse("show");
    let div = document.getElementById("divPdf");

    var opt = {
        margin: 1,
        filename: name,
        html2canvas: {scale: window.devicePixelRatio, scrollY: 0},
        jsPDF: {format: 'a4', orientation: 'landscape' },
        pagebreak: {before: ".breakPage"}
        // mode: "avoid-all",
    };
    html2pdf().set(opt).from(div).save();
}