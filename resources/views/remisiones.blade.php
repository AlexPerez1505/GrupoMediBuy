<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotizaciones</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-5xl mx-auto bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl font-semibold">Cotizaciones</h2>
        <p class="text-gray-500">Crea cotizaciones y envíalas a tus clientes</p>
        
        <div class="mt-4 flex justify-between items-center">
            <input type="text" placeholder="Filtrar por número de cotización, cliente, vendedor o productos" class="w-2/3 p-2 border rounded-lg">
            <div class="flex gap-2">
                <button class="px-4 py-2 border rounded-lg bg-gray-200">Estado ▼</button>
                <button class="px-4 py-2 border rounded-lg bg-gray-200">Ordenar ▼</button>
                <button class="px-4 py-2 bg-teal-500 text-white rounded-lg">+ Crear Cotización</button>
            </div>
        </div>

        <div class="mt-4 bg-white border rounded-lg">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b">
                        <th class="p-3"><input type="checkbox"></th>
                        <th class="p-3">Cotización</th>
                        <th class="p-3">Fecha</th>
                        <th class="p-3">Creada Por</th>
                        <th class="p-3">Cliente</th>
                        <th class="p-3">Estado</th>
                        <th class="p-3">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b hover:bg-gray-100">
                        <td class="p-3"><input type="checkbox"></td>
                        <td class="p-3 text-blue-600 cursor-pointer">#6</td>
                        <td class="p-3">Hoy a las 11:05</td>
                        <td class="p-3">Jose Alex E.</td>
                        <td class="p-3">-</td>
                        <td class="p-3"><span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full">Creada</span></td>
                        <td class="p-3">$50.00</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
