$(document).ready(function ()
{
    $(document).ready(function() {
        $('.TableVente').DataTable({
            language: {
                "sInfo": "Affichage de l'élément _START_ à _END_ sur _TOTAL_ éléments",
                "sInfoEmpty": "Affichage de l'élément 0 à 0 sur 0 élément",
                "sInfoFiltered": "(filtré à partir de _MAX_ éléments au total)",
                "sInfoPostFix": "",
                "sInfoThousands": ",",
                "sLengthMenu": "Afficher _MENU_ éléments",
                "sLoadingRecords": "Chargement...",
                "sProcessing": "Traitement...",
                "sSearch": "Rechercher :",
                "sZeroRecords": "Aucun élément correspondant trouvé",
                "oPaginate": {
                    "sFirst": "Premier",
                    "sLast": "Dernier",
                    "sNext": "Suivant",
                    "sPrevious": "Précédent"
                },
                "oAria": {
                    "sSortAscending": ": activer pour trier la colonne par ordre croissant",
                    "sSortDescending": ": activer pour trier la colonne par ordre décroissant"
                },
                "select": {
                    "rows": {
                        "_": "%d lignes sélectionnées",
                        "0": "Aucune ligne sélectionnée",
                        "1": "1 ligne sélectionnée"
                    }
                }
            }
        });
    });
    var currentMonth = new Date().getMonth() + 1; // Get the current month (0-11)
    var formattedMonth = ('0' + currentMonth).slice(-2); // Format month to 2 digits
    $('#selectMonth').val(formattedMonth);
    var chartLine; // Global variable to hold the chart instance

    function FunctionChartCredit(Year, Month, click_Search) {
        // Ensure that click_Search is passed and used correctly
        if (click_Search === undefined) {
            click_Search = false;
        }

        if (!click_Search) {
            console.log(click_Search);
            Year = new Date().getFullYear();
            Month = new Date().getMonth() + 1;
        }

        $.ajax({
            type: "get",
            url: ChartCredit,
            data: {
                Year: Year,
                Month: Month,
                Click: click_Search, // Pass the correct click_Search value
            },
            dataType: "json",
            success: function (response) {
                if (response.status == 200) {
                    if (!click_Search) {
                        $('#selectYear').empty();
                        $.each(response.years, function (index, value) {
                            $('#selectYear').append('<option value="' + value + '">' + value + '</option>');
                        });
                    }

                    // Clear the existing chart if it exists
                    if (chartLine) {
                        chartLine.destroy();
                    }

                    // Create new chart configuration
                    var optionsLine = {
                        chart: {
                            height: 328,
                            type: 'line',
                            zoom: {
                                enabled: false
                            },
                            dropShadow: {
                                enabled: true,
                                top: 3,
                                left: 2,
                                blur: 4,
                                opacity: 1,
                            }
                        },
                        stroke: {
                            curve: 'smooth',
                            width: 2
                        },
                        series: [{
                            name: "Total Credit",
                            data: response.totals // Injecting the PHP array into JavaScript
                        }],
                        title: {
                            text: 'Crédit total par jour',
                            align: 'left',
                            offsetY: 25,
                            offsetX: 20
                        },
                        subtitle: {
                            text: '',
                            offsetY: 55,
                            offsetX: 20
                        },
                        markers: {
                            size: 6,
                            strokeWidth: 0,
                            hover: {
                                size: 9
                            }
                        },
                        grid: {
                            show: true,
                            padding: {
                                bottom: 0
                            }
                        },
                        xaxis: {
                            categories: response.labels, // Correctly set as categories
                            tooltip: {
                                enabled: false
                            }
                        },
                        legend: {
                            position: 'top',
                            horizontalAlign: 'right',
                            offsetY: -20
                        }
                    };

                    // Create a new chart instance and render it
                    chartLine = new ApexCharts(document.querySelector('#line-adwords'), optionsLine);
                    chartLine.render();
                }
            }
        });
    }

    // Initial call with default values
    FunctionChartCredit();

    // Event listener to update click_Search value and call the function
    $('#BtnSearchChartCredit').on('click', function (e) {
        e.preventDefault();
        var Year = $('#selectYear').val();
        var Month = $('#selectMonth').val();
        var click_Search = true; // Correctly update the click_Search value
        FunctionChartCredit(Year, Month, click_Search);
    });




});
