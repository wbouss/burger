$(document).ready(function () {
    $('#tabPanier').DataTable({
        "sAjaxSource": Routing.generate('panier_data'),
        "aoColumns": [
            {
                "className": 'details-control',
                "orderable": false,
                "data": null,
                "defaultContent": ''
            },
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
            var imgProduit = "/burger/web/" + data["imageProduit"];
            $('td', row).eq(1).html("<img src='" + imgProduit + "' width='80'/>");
            $('td', row).eq(2).html("<a href=''>" + data["libelleProduit"] + "</a><br>" + data["descriptionProduit"]);
            $('td', row).eq(3).html(data["prixProduit"] + " €");
            $('td', row).eq(4).html('<i class="fa fa-plus-circle fa-lg" aria-hidden="true"  onmouseover="this.style.cursor=\'pointer\'" onClick="location.href=\'' + Routing.generate("burger_ajoutpanier", {"produitId": data["IdProduit"]}) + '\'"></i> <i class="fa fa-minus-circle fa-lg" aria-hidden="true"   onmouseover="this.style.cursor=\'pointer\'" onClick="location.href=\'' + Routing.generate("burger_reduirepanier", {"produitId": data["IdProduit"]}) + '\'" ></i> <input type="text" id="inputTextQuantite" value="' + data['qteProduit'] + '"  readonly/> <i class="fa fa-times fa-lg" aria-hidden="true"  onmouseover="this.style.cursor=\'pointer\'" onClick="location.href=\'' + Routing.generate("burger_supprimerpanier", {"produitId": data["IdProduit"]}) + '\'" ></i>');

            $('td', row).eq(5).html(data["prixProduit"] * data["qteProduit"] + " €");
        }
    });
});
// Add event listener for opening and closing details
$('#tabPanier tbody').on('click', 'td.details-control', function () {
    var tr = $(this).closest('tr');
    var row = table.row(tr);

    if (row.child.isShown()) {
        // This row is already open - close it
        row.child.hide();
        tr.removeClass('shown');
    } else {
        // Open this row
        row.child(format(row.data())).show();
        tr.addClass('shown');
    }
});
function format(d) {
    return 'dfgd';
}