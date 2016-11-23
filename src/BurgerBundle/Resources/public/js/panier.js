$(document).ready(function () {
    $('#tabPanier').DataTable({
        "sAjaxSource": Routing.generate('panier_data'),
        "aoColumns": [
            {"mDataProp": "libelleProduit", "width": "80"},
            {"mDataProp": "libelleProduit"},
            {"mDataProp": "prixProduit"},
            {"mDataProp": "qteProduit"},
            {"mDataProp": "qteProduit"}
        ],
        "bFilter": false,
        "paging": false,
        "oLanguage": {"sProcessing": "Traitement en cours...",
            "sLengthMenu": "",
            "sZeroRecords": "Votre panier est vide",
            "sInfo": "",
            "sInfoEmpty": "",
            "sInfoFiltered": "(filtré de _MAX_ éléments au total)",
            "sInfoPostFix": "",
            "sSearch": "Rechercher:",
            "sUrl": "",
            "oPaginate": {
                "sFirst": "Premier",
                "sPrevious": "Précédent",
                "sNext": "Suivant",
                "sLast": "Dernier"
            }
        },
        "createdRow": function (row, data, index) {
            var imgProduit = "/burger/web/"+data["imageProduit"];
            $('td', row).eq(0).html("<img src='"+imgProduit+"' width='80'/>");
            $('td', row).eq(1).html("<a href=''>"+data["libelleProduit"]+"</a><br>"+data["descriptionProduit"]);
            $('td', row).eq(2).html(data["prixProduit"]+" €");
            
            $('td', row).eq(4).html(data["prixProduit"]*data["qteProduit"]+" €");
        }
    });
});