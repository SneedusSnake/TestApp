export async function fetchCountries() {
    const response = await fetch('http://localhost/api/countries')

    return (await response.json()).data
}
