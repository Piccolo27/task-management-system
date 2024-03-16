const inputClass = 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 ' +
    'focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 ' +
    'dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500'

export const projectInputs = {
    name: {
        type: 'text',
        name: 'name',
        id: 'name',
        class: inputClass,
        label: {
            for: 'name',
            text: 'Project Name',
            isRequired: true
        }
    },
    language: {
        type: 'text',
        name: 'language',
        id: 'language',
        class: inputClass,
        label: {
            for: 'language',
            text: 'Language',
            isRequired: true
        }
    },
    description: {
        type: 'text',
        name: 'description',
        id: 'description',
        class: inputClass,
        label: {
            for: 'description',
            text: 'Description',
            isRequired: false
        }
    },
    startDate: {
        type: 'date',
        name: 'start_date',
        id: 'start_date',
        label: {
            for: 'start_date',
            text: 'Start Date',
            isRequired: false
        }
    },
    endDate: {
        type: 'date',
        name: 'end_date',
        id: 'end_date',
        label: {
            for: 'end_date',
            text: 'End Date',
            isRequired: false
        }
    }
}

export const projectSubmitBtn = {
    type: 'submit',
    class: 'inline-flex items-center px-5 py-2.5 mt-4 sm:mt-6 text-sm font-medium text-center text-white bg-primary-700 ' +
        'rounded-lg focus:ring-4 focus:ring-primary-200 dark:focus:ring-primary-900 hover:bg-primary-800 me-3',
    text: 'Save'
}