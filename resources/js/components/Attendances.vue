<template>
<table class="p-0 w-full text-left">
    <thead>
        <tr>
            <th class="px-8 py-4 border-b border-20 uppercase tracking-wide text-sm text-50">Date</th>
            <th class="px-6 py-4 border-b border-20 uppercase tracking-wide text-sm text-50">Time In</th>
            <th class="px-6 py-4 border-b border-20 uppercase tracking-wide text-sm text-50">Time Out</th>
            <th class="px-6 py-4 border-b border-20 uppercase tracking-wide text-sm text-50"></th>
            <th class="px-6 py-4 border-b border-20 uppercase tracking-wide text-sm text-50">&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        <tr v-show="canTimein || recentHasTimedout" class="bg-blue-100">
            <td class="px-6 py-3 border-b border-20 text-90 font-normal">
                {{ now.format('dddd') }}<br>
                <small class="text-gray-600">{{ now.format('MMM DD, YYYY') }}</small>
            </td>
            <td class="px-6 py-3 border-b border-20 text-90 font-normal">--</td>
            <td class="px-6 py-3 border-b border-20 text-90 font-normal">--</td>
            <td class="px-6 py-3 border-b border-20 text-90 font-normal"></td>
            <td class="px-6 py-3 border-b border-20 text-90 font-normal text-right">
                <a @click="timeIn" href="#" class="bg-primary text-white no-underline px-3 py-2 rounded font-bold uppercase tracking-wide whitespace-no-wrap hover:no-underline">{{ now.format('hh:mm A') }} In</a>
            </td>
        </tr>
        <tr v-for="(attendance, index) in attendances">
            <td class="px-6 py-3 border-b border-20 text-90 font-normal">
                {{ attendance.in && attendance.in.time.format('dddd') }}<br>
                <small class="text-gray-600">{{ attendance.in && attendance.in.time.format('MMM DD, YYYY') }}</small>
            </td>
            <td class="px-6 py-3 border-b border-20 text-90 font-normal">{{ attendance.in && attendance.in.time.format('hh:mm A') }}<br><small class="text-gray-600" :title="attendance.in && attendance.in.ip_address">{{ attendance.in && (attendance.in.alias || attendance.in.ip_address) }}</small></td>
            <td class="px-6 py-3 border-b border-20 text-90 font-normal">{{ attendance.out ? attendance.out.time.format('hh:mm A') : '--' }}<br><small class="text-gray-600" :title="get(attendance, 'out.ip_address')">{{ attendance.out ? attendance.out.alias || attendance.out.ip_address : '&nbsp;' }}</small></td>
            <td class="px-6 py-3 border-b border-20 text-90 font-normal">
                <div class="w-32">
                    <div class="shadow-sm w-full bg-gray-100 rounded mt-2">
                        <div class="bg-green-600 text-xs leading-none py-1 text-center text-white" :class="percentage(attendance) >= 100 ? 'rounded' : 'rounded-l'" :style="`width: ${percentage(attendance)}%`">{{ hours(attendance.in.time, attendance.out ? attendance.out.time : now) }}h</div>
                    </div>
                </div>
            <td class="px-6 py-3 border-b border-20 text-90 font-normal text-right">
                <a v-if="!attendance.out || (attendance.out && !attendance.out.time)" @click="timeOut" href="#" class="bg-primary text-white no-underline px-3 py-2 rounded font-bold uppercase tracking-wide whitespace-no-wrap hover:no-underline">{{ now.format('hh:mm A') }} Out</a>
                <template v-if="attendance.out && !attendance.out.time">
                    <!-- <a href="#" class="border-2 border-gray-500 hover:no-underline text-gray-500 hover:text-gray-600 hover:border-gray-600 no-underline px-3 py-2 rounded font-bold uppercase tracking-wide whitespace-no-wrap">6:00PM</a>
                    <a href="#" class="bg-primary text-white no-underline px-3 py-2 rounded font-bold uppercase tracking-wide whitespace-no-wrap">6:00AM In</a>
                    <a href="#" class="bg-primary text-white no-underline px-3 py-2 rounded font-bold uppercase tracking-wide whitespace-no-wrap">In</a>
                    <a href="#" class="bg-primary text-white no-underline px-3 py-2 rounded font-bold uppercase tracking-wide whitespace-no-wrap">Out</a> -->
                </template>
            </td>
        </tr>
    </tbody>
</table>
</template>

<script>
import get from 'lodash/get'

export default {
    data () {
        return {
            ip: null,
            now: moment(),
            results: []
        }
    },

    computed: {
        attendances () {
            let groups = _.groupBy(_.map(this.results, (result) => {
                return Object.assign(result, {
                    time: moment(result.time)
                })
            }), (attendance) => {
                let time = moment(attendance.time).format('YYYY-MM-DD')

                return `${time}-${attendance.entry}`
            })

            return _.map(groups, (items) => {
                return {
                    in: _.find(items, ['type', 'in']),
                    out: _.find(items, ['type', 'out']),
                }
            })
        },

        canTimein () {
            let noEntry = !! get(this.attendances, moment().format('YYYY-MM-DD'))

            return noEntry || !this.attendances.length
        },

        recentHasTimedout () {
            let attendance = _.head(this.attendances)

            if (attendance) {
                return !! get(attendance, 'out.time')
            }
        }
    },

    created () {
        setInterval(() => this.now = moment(), 1000)
    },

    async mounted () {
        await this.fetchAttendances()
        await this.fetchIp()
    },

    methods: {
        async fetchIp () {
            const response = await fetch('https://ipv4.icanhazip.com/')
            const ip = await response.text()

            this.ip = _.trim(ip)
        },

        async fetchAttendances () {
            const { data: { data } } = await axios.get('attendances')

            this.results = data
        },

        async saveAttendance (type) {
            await axios.post('attendances', {
                type,
                ip_address: this.ip,
                time: moment().format(),
                location: 'testing',
                notes: 'testing'
            })

            success({
                text: `Timed ${type}`,
            })

            this.fetchAttendances()
        },

        timeIn () {
            this.saveAttendance('in')
        },

        timeOut () {
            this.saveAttendance('out')
        },

        hours (timeIn, timeOut) {
            return timeOut.diff(timeIn, 'hours')
        },

        percentage (attendance) {
            let hours = this.hours(attendance.in.time, attendance.out ? attendance.out.time : moment())

            return hours > 9 ? 100 : (hours/9)*100
        },

        get
    }
}
</script>
