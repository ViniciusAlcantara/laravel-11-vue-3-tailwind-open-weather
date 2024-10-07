<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, ref } from 'vue';

const forecasts = ref([]);
const locationSearch = defineModel({ default: '' })
const openWeatherIconUrl = ref('https://openweathermap.org/img/wn/')
const has_error = ref(false)
const error_message = ref('')

const submitSearch = async () => {
    try {
        if (locationSearch.value === '' || locationSearch.value.length <= 3) return

        const { data } = await axios.post('/forecasts', {
            location: locationSearch.value
        })

        const index = forecasts.value.findIndex(forecast => forecast.location === data.location)

        if (index !== -1) {
            forecasts.value.splice(index, 1)
        }
        
        forecasts.value.unshift(data)
    } catch (error) {
        if (error?.response?.status === 400) {
            error_message.value = error?.response?.data.message ?? error?.response?.data.error_message
        } else {
            error_message.value = 'An error happened while submitting location search!'
        }
        has_error.value = true
    }
}

const getForecasts = async () => {
    try {
        const { data } = await axios.get('/forecasts')
        
        forecasts.value = data
    } catch (error) {
        if (error?.response?.status === 400) {
            error_message.value = error?.response?.data.error_message
        } else {
            error_message.value = 'An error happened while searching your locations!'
        }
        has_error.value = true
    }
}

const formatTemp = (number) => {
    return Math.ceil(number)
}

const removeLocation = async (location) => {
    try {
        await axios.delete(`/forecasts/${location}`)

        const index = forecasts.value.findIndex(forecast => forecast.location === location)

        if (index !== -1) {
            forecasts.value.splice(index, 1)
        }
    } catch (error) {
        if (error?.response?.status === 400) {
            error_message.value = error?.response?.data.error_message
        } else {
            error_message.value = 'An error happened while removing location!'
        }
        has_error.value = true
    }
}

const dismissError = () => {
    has_error.value = false
    error_message.value = ''
}

onMounted(() => {
    getForecasts()
})

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="w-full inline-flex flex-col justify-center relative text-gray-500">
                <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                    <input
                        type="search"
                        id="default-search"
                        class="block w-full p-4 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Search a location, i.e.: London,UK"
                        required
                        v-model="locationSearch"
                    />
                    <button
                        class="text-white absolute end-2.5 bottom-2.5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                        @click="submitSearch"
                    >
                        Search
                    </button>
                </div>
            </div>
        </template>

        <div class="grid grid-cols-3 gap-3 w-screen min-h-screen text-gray-700 p-4">
            <div id="toast-top-right" class="fixed flex items-center w-full max-w-xs p-4 space-x-4 text-gray-500 bg-white divide-x rtl:divide-x-reverse divide-gray-200 rounded-lg shadow top-5 right-5 dark:text-gray-400 dark:divide-gray-700 dark:bg-gray-800" role="alert" v-if="has_error">
                <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-red-500 bg-red-100 rounded-lg dark:bg-red-800 dark:text-red-200">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 11.793a1 1 0 1 1-1.414 1.414L10 11.414l-2.293 2.293a1 1 0 0 1-1.414-1.414L8.586 10 6.293 7.707a1 1 0 0 1 1.414-1.414L10 8.586l2.293-2.293a1 1 0 0 1 1.414 1.414L11.414 10l2.293 2.293Z"/>
                    </svg>
                    <span class="sr-only">Error icon</span>
                </div>
                <div class="ms-3 text-sm font-normal">{{ error_message }}</div>
                <button 
                    type="button"
                    class="ms-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700"
                    data-dismiss-target="#toast-danger"
                    aria-label="Close"
                    @click="dismissError"
                >
                    <span class="sr-only">Close</span>
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                </button>
            </div>
            <div class="flex flex-col min-h-screen text-gray-700 p-10"  v-for="(forecast, index) in forecasts" :key="index">
                <!-- Component Start -->
                <div class="max-w-screen-sm bg-white p-5 rounded-xl ring-8 ring-white ring-opacity-40">
                    <div class="flex justify-between">
                        <div class="flex flex-col justify-between">
                            <span class="text-4xl font-bold">{{ formatTemp(forecast.temp_min) }}° / {{ formatTemp(forecast.temp_max) }}°C</span>
                            <span class="font-semibold mt-1 text-gray-500"> {{ forecast.location }}</span>
                            <button
                                type="button"
                                class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900"
                                @click="removeLocation(forecast.location)"
                            >
                                Remove Location
                            </button>
                        </div>
                        
                        <div class="flex flex-col justify-between">
                            <img class="h-24 w-24 fill-current text-yellow-400" :src="`${openWeatherIconUrl}/${forecast.weather_icon}@2x.png`" />
                            <span class="font-semibold mt-1 text-gray-500 text-center">  {{ forecast.weather }} </span>
                            <span class="text-sm mt-1 text-gray-500 text-center">{{ forecast.weather_description }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col space-y-6 w-full max-w-screen-sm bg-white p-10 mt-10 rounded-xl ring-8 ring-white ring-opacity-40">
                    <div class="flex justify-between items-center" v-for="(other_days_forecast, index) in forecast.other_days_forecasts" :key="index">
                        <span class="font-semibold text-lg w-1/4">{{ other_days_forecast.date_front }}</span>
                        
                        <img class="h-10 w-10 fill-current text-gray-400 mt-3" :src="`${openWeatherIconUrl}/${other_days_forecast.weather_icon}@4x.png`"/>
                        <div class="flex flex-col">
                            <span class="font-semibold mt-1 text-gray-500 text-center"> {{ other_days_forecast.weather }}</span>
                            <span class="text-sm mt-1 text-gray-500 text-center">{{ other_days_forecast.weather_description }}</span>
                        </div>
                        <span class="font-semibold text-lg w-1/4 text-right">{{ formatTemp(other_days_forecast.temp_min) }}° / {{ formatTemp(other_days_forecast.temp_max) }}°C</span>
                    </div>
                </div>
                <!-- Component End  -->

            </div>
        </div>
    </AuthenticatedLayout>
</template>