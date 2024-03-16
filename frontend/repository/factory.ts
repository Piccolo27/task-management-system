import { $Fetch, FetchOptions } from "ofetch";
import {UNAUTHORIZED_ERROR_CODE} from "~/constants/constants";

/*
The FetchFactory acts as a wrapper around an HTTP client.
It encapsulates the functionality for making API requests asynchronously
through the call function, utilizing the provided HTTP client.
*/
class FetchFactory {
    private readonly $fetch: $Fetch

    constructor(fetcher: $Fetch) {
        this.$fetch = fetcher;
    }

    /**
     * The HTTP client is utilized to control the process of making API requests.
     *
     * @param method the HTTP method (GET, POST, ...)
     * @param url the endpoint url
     * @param data the body data
     * @param fetchOptions fetch options
     * @returns
     */
    async call(
        method: string,
        url: string,
        data?: object,
        fetchOptions: FetchOptions<'json'> = {}
    ) {
        const { headers, ...rest } = fetchOptions as { headers: Record<string, string> }
        const jwtToken = useCookie('jwt-token').value as string
        const csrfToken =  useCookie('XSRF-TOKEN').value as string

        const requestHeaders = {
            'Accept': 'application/json',
            'X-XSRF-TOKEN': csrfToken,
            ...(headers || {})
        }

        if (jwtToken) {
            // @ts-ignore
            requestHeaders['Authorization'] = `Bearer ${jwtToken}`
        }

        return this.$fetch(
            url,
            {
                method,
                body: data,
                credentials: "include",
                headers: requestHeaders,
                ...(rest || {})
            }
        )
    }
}

export default FetchFactory;