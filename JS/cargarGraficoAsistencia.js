am5.ready(function () {

    fetch('controladores/obtenerDatosGrafica.php')
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then(datos => {
        if (datos.error) {
          throw new Error(datos.error);
        }


        var data = datos;


        var root = am5.Root.new("chartdiv");


        root.setThemes([
          am5themes_Animated.new(root)
        ]);


        var chart = root.container.children.push(am5xy.XYChart.new(root, {
          panX: false,
          panY: false,
          wheelX: "panX",
          wheelY: "zoomX",
          paddingLeft: 0,
          layout: root.verticalLayout
        }));


        var legend = chart.children.push(am5.Legend.new(root, {
          centerX: am5.p50,
          x: am5.p50
        }))

        var yAxis = chart.yAxes.push(am5xy.CategoryAxis.new(root, {
          categoryField: "nombre",
          renderer: am5xy.AxisRendererY.new(root, {
            inversed: true,
            cellStartLocation: 0.1,
            cellEndLocation: 0.9,
            minorGridEnabled: true
          })
        }));

        yAxis.data.setAll(data);

        var xAxis = chart.xAxes.push(am5xy.ValueAxis.new(root, {
          renderer: am5xy.AxisRendererX.new(root, {
            strokeOpacity: 0.1,
            minGridDistance: 50
          }),
          min: 0
        }));

        function createSeries(field, name) {
          var series = chart.series.push(am5xy.ColumnSeries.new(root, {
            name: name,
            xAxis: xAxis,
            yAxis: yAxis,
            valueXField: field,
            categoryYField: "nombre",
            sequencedInterpolation: true,
            tooltip: am5.Tooltip.new(root, {
              pointerOrientation: "horizontal",
              labelText: "[bold]{name}[/]\n{categoryY}: {valueX}"
            })
          }));

          series.columns.template.setAll({
            height: am5.p100,
            strokeOpacity: 0
          });


          series.bullets.push(function () {
            return am5.Bullet.new(root, {
              locationX: 1,
              locationY: 0.5,
              sprite: am5.Label.new(root, {
                centerY: am5.p50,
                text: "{valueX}",
                populateText: true
              })
            });
          });

          series.bullets.push(function () {
            return am5.Bullet.new(root, {
              locationX: 1,
              locationY: 0.5,
              sprite: am5.Label.new(root, {
                centerX: am5.p100,
                centerY: am5.p50,
                text: "{name}",
                fill: am5.color(0xffffff),
                populateText: true
              })
            });
          });

          series.data.setAll(data);
          series.appear();

          return series;
        }

        createSeries("asistencias", "Asistencias");
        createSeries("permisos", "Permisos");

        var legend = chart.children.push(am5.Legend.new(root, {
          centerX: am5.p50,
          x: am5.p50
        }));

        legend.data.setAll(chart.series.values);

        var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {
          behavior: "zoomY"
        }));
        cursor.lineY.set("forceHidden", true);
        cursor.lineX.set("forceHidden", true);
        chart.appear(1000, 100);

      });
  })
    .catch(error => console.error('Error al obtener los datos:', error));

