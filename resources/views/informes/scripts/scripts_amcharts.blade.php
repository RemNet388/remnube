<script src="https://cdn.amcharts.com/lib/5/index.js"></script>
<script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
<script src="https://cdn.amcharts.com/lib/5/percent.js"></script>
<script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>

<script>
const dataMes = @json($ventasMensuales ?? []);
const dataCategoria = @json($ventasPorCategoria ?? []);
const dataProductos = @json($masVendidos ?? []);

am5.ready(function() {

    // ===== Ventas por MES (LINE CHART) =====
    if(dataMes.length > 0){
        let rootMes = am5.Root.new("chartMes");
        rootMes.setThemes([am5themes_Animated.new(rootMes)]);

        let chartMes = rootMes.container.children.push(
            am5xy.XYChart.new(rootMes, { panX: true, panY: true, wheelX: "panX", wheelY: "zoomX", pinchZoomX:true })
        );

        let xAxisMes = chartMes.xAxes.push(
            am5xy.CategoryAxis.new(rootMes, { categoryField: "mes", renderer: am5xy.AxisRendererX.new(rootMes, { minGridDistance: 30 }) })
        );

        let yAxisMes = chartMes.yAxes.push(
            am5xy.ValueAxis.new(rootMes, { renderer: am5xy.AxisRendererY.new(rootMes, {}) })
        );

        let seriesMes = chartMes.series.push(
            am5xy.LineSeries.new(rootMes, {
                name: "Ventas",
                xAxis: xAxisMes,
                yAxis: yAxisMes,
                valueYField: "total",
                categoryXField: "mes",
                tooltip: am5.Tooltip.new(rootMes, { labelText: "{valueY}" })
            })
        );

        seriesMes.bullets.push(function() {
            return am5.Bullet.new(rootMes, {
                sprite: am5.Circle.new(rootMes, { radius: 4, fill: seriesMes.get("fill") })
            });
        });

        seriesMes.data.setAll(dataMes);
        xAxisMes.data.setAll(dataMes);
        seriesMes.appear(1000);
        chartMes.appear(1000, 100);
    }

    // ===== Ventas por CATEGORÍA (PIE CHART) =====
    if(dataCategoria.length > 0){
        let rootCat = am5.Root.new("chartCategoria");
        rootCat.setThemes([am5themes_Animated.new(rootCat)]);

        let chartCat = rootCat.container.children.push(
            am5percent.PieChart.new(rootCat, { layout: rootCat.verticalLayout })
        );

        let seriesCat = chartCat.series.push(
            am5percent.PieSeries.new(rootCat, { valueField: "total", categoryField: "categoria" })
        );

        // Mapear para evitar nombres vacíos
        seriesCat.data.setAll(dataCategoria.map(c => ({
            categoria: c.categoria ?? 'Sin Categoría',
            total: c.total
        })));

        // Texto más pequeño
        seriesCat.labels.template.setAll({ 
            text: "{category}: {value}", 
            fontSize: 12 
        });
    }

    // ===== Productos MÁS VENDIDOS DEL MES (COLUMN CHART) =====
if(dataProductos.length > 0){
    let rootProd = am5.Root.new("chartProductos");
    rootProd.setThemes([am5themes_Animated.new(rootProd)]);

    let chartProd = rootProd.container.children.push(
        am5xy.XYChart.new(rootProd, {})
    );

    let xAxisProd = chartProd.xAxes.push(
        am5xy.CategoryAxis.new(rootProd, {
            categoryField: "producto",
            renderer: am5xy.AxisRendererX.new(rootProd, { minGridDistance: 30 })
        })
    );

    let yAxisProd = chartProd.yAxes.push(
        am5xy.ValueAxis.new(rootProd, {
            renderer: am5xy.AxisRendererY.new(rootProd, {})
        })
    );

    let seriesProd = chartProd.series.push(
        am5xy.ColumnSeries.new(rootProd, {
            name: "Cantidad Vendida",
            xAxis: xAxisProd,
            yAxis: yAxisProd,
            valueYField: "total",
            categoryXField: "producto",
            tooltip: am5.Tooltip.new(rootProd, { labelText: "{valueY}" })
        })
    );

    // Solo top 8 y mapear nombres
    seriesProd.data.setAll(
        dataProductos
            .sort((a,b) => b.total - a.total)
            .slice(0,8)
            .map(p => ({
                producto: p.producto ?? 'Sin nombre',
                total: p.total
            }))
    );

    xAxisProd.data.setAll(seriesProd.dataItems.map(di => di.dataContext));

    // ===== Ajustes visuales de los nombres =====
    xAxisProd.get("renderer").labels.template.setAll({
        rotation: -45,          // inclinación
        fontSize: 11,           // más pequeña
        maxWidth: 80,           // ancho máximo antes de hacer wrap
        oversizedBehavior: "wrap",
        centerX: am5.p50,
        centerY: am5.p50,
        paddingRight: 5
    });

    seriesProd.appear(1000);
    chartProd.appear(1000, 100);
}

});
</script>
