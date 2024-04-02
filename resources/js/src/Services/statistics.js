export async function fetchSales(date_from, date_to) {
    let url = 'http://localhost/api/statistics/sales'
    if (date_from || date_to) {
        url += '?' + new URLSearchParams({
            ...(date_from ? {date_from} : null),
            ...(date_to ? {date_to} : null),
        })
    }
    const response = await fetch(url)
    const json = await response.json()

    return json.data
}

export async function fetchClients(date_from, date_to) {
    let url = 'http://localhost/api/statistics/clients'
    if (date_from || date_to) {
        url += '?' + new URLSearchParams({
            ...(date_from ? {date_from} : null),
            ...(date_to ? {date_to} : null),
        })
    }
    const response = await fetch(url)
    const json = await response.json()

    return json.data
}
