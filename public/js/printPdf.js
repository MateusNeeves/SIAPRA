$('#btnPdf').click(()=>{
    let pdf = new jsPDF('p','pt','a4');
    let btn = document.getElementById('btnPdf');
    let div = document.getElementById('divPdf');
    pdf.addHTML(div, function() {
        pdf.save(btn.name);
    });
})


