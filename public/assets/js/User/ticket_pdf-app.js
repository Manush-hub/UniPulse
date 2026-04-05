
/* Extracted from User/ticket_pdf.view.php */

        // Auto-trigger print prompt when loaded so it acts as a download
        window.onload = function() { 
            setTimeout(() => { window.print(); }, 500); 
        }
    
