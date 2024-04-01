export async function fetchSales() {
    const response = await fetch('http://localhost/api/statistics/sales')
    const json = await response.json()

    return json.data
}

export async function fetchClients() {
    const response = await fetch('http://localhost/api/statistics/clients')
    const json = await response.json()

    return json.data
}
