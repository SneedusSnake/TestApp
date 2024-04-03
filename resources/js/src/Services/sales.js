export async function fetchSales(filters) {
    let url = 'http://localhost/api/sales'
    if (filters) {
        url += '?' + new URLSearchParams(filters)
    }

    const response = await fetch(url)

    return await response.json()
}
