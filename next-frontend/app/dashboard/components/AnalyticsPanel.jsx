"use client";
import { useDashboardStats } from "../../hooks/use-dashboard";
import { useState } from "react";
import {
    Eye,
    MousePointerClick,
    Users,
    Phone,
    MessageCircle,
    MessageSquare,
    Mail,
    CalendarDays,
    BarChart3,
} from "lucide-react";

export default function AnalyticsPanel() {
    const { data: stats } = useDashboardStats();
    const [activeFilter, setActiveFilter] = useState("all");
    const [activeMetric, setActiveMetric] = useState("clicks");

    const analytics = stats?.analytics || {
        views: 0,
        clicks: 0,
        leads: 0,
        calls: 0,
        whatsapp: 0,
        sms: 0,
        emails: 0,
    };

    return (
        <div className="rounded-xl border border-slate-200 shadow-sm overflow-hidden flex flex-col bg-white">

            {/* Header */}
            <div className="p-4 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <h2 className="text-lg font-bold text-slate-800">Analytics</h2>

                <div className="flex flex-wrap items-center gap-3">
                    <div className="bg-slate-100 p-1 rounded-lg flex items-center">
                        {[
                            { id: "all", label: "All" },
                            { id: "sale", label: "For Sale" },
                            { id: "rent", label: "For Rent" },
                        ].map((filter) => (
                            <button
                                key={filter.id}
                                onClick={() => setActiveFilter(filter.id)}
                                className={`px-4 py-1.5 text-xs font-bold rounded-md transition-all uppercase
    ${activeFilter === filter.id
                                        ? "bg-green-600 text-white shadow-sm"
                                        : "bg-slate-100 text-slate-600 hover:bg-slate-200"
                                    }`}
                            >
                                {filter.label}
                            </button>
                        ))}
                    </div>

                    <button className="border border-slate-200 text-slate-600 font-semibold h-9 rounded-lg px-4 flex items-center">
                        Last 30 Days
                        <CalendarDays className="w-4 h-4 ml-2 text-primary" />
                    </button>
                </div>
            </div>

            {/* Metrics Row */}
            <div className="flex flex-col lg:flex-row border-b border-slate-100">

                {/* Left Metrics */}
                <div className="flex w-full lg:w-3/5 divide-x divide-slate-100 border-b lg:border-b-0">
                    {[
                        { id: "views", label: "Views", count: analytics.views, icon: Eye },
                        { id: "clicks", label: "Clicks", count: analytics.clicks, icon: MousePointerClick },
                        { id: "leads", label: "Leads", count: analytics.leads, icon: Users },
                    ].map((metric) => (
                        <div
                            key={metric.id}
                            onClick={() => setActiveMetric(metric.id)}
                            className={`flex-1 p-6 cursor-pointer transition-colors relative
    ${activeMetric === metric.id
                                    ? "bg-white border-t-2 border-emerald-600"
                                    : "bg-slate-50/30"
                                }`}
                        >
                            {activeMetric === metric.id && (
                                <div className="absolute top-0 left-0 right-0 h-0.5 bg-primary" />
                            )}

                            <div className="flex items-center gap-3 mb-2">
                                <div
                                    className={`w-8 h-8 rounded-lg flex items-center justify-center ${activeMetric === metric.id ? "bg-primary/10" : "bg-white"
                                        }`}
                                >
                                    <metric.icon
                                        className={`w-4 h-4 ${activeMetric === metric.id
                                                ? "text-primary"
                                                : "text-slate-400"
                                            }`}
                                    />
                                </div>

                                <span className="text-sm font-medium text-slate-500">
                                    {metric.label}
                                </span>
                            </div>

                            <div className="flex items-baseline gap-2">
                                <h3 className="text-2xl font-bold text-slate-900">
                                    {metric.count}
                                </h3>
                                <span className="text-xs font-medium text-slate-400 uppercase">
                                    No Data
                                </span>
                            </div>
                        </div>
                    ))}
                </div>

                {/* Right Metrics */}
                <div className="flex w-full lg:w-2/5 divide-x divide-slate-100 bg-slate-50/30">
                    {[
                        { id: "calls", label: "Calls", count: analytics.calls, icon: Phone },
                        { id: "whatsapp", label: "WhatsApp", count: analytics.whatsapp, icon: MessageCircle },
                        { id: "sms", label: "SMS", count: analytics.sms, icon: MessageSquare },
                        { id: "emails", label: "Emails", count: analytics.emails, icon: Mail },
                    ].map((metric) => (
                        <div
                            key={metric.id}
                            className="flex-1 p-4 flex flex-col items-center justify-center"
                        >
                            <metric.icon className="w-5 h-5 text-slate-400 mb-2" />

                            <div className="flex items-baseline gap-1">
                                <h4 className="text-lg font-bold text-slate-900 leading-none">
                                    {metric.count}
                                </h4>
                                <span className="text-[10px] font-medium text-slate-400 uppercase">
                                    No Data
                                </span>
                            </div>

                            <span className="text-[10px] font-medium text-slate-500 mt-1 uppercase">
                                {metric.label}
                            </span>
                        </div>
                    ))}
                </div>
            </div>

            {/* Chart Area */}
            <div className="p-12 flex flex-col items-center justify-center min-h-[300px] bg-white">
                <div className="relative mb-6">
                    <div className="w-20 h-16 border-2 border-emerald-500 rounded-lg flex items-center justify-center relative">
                        <div className="absolute -top-3 -right-3 w-6 h-6 bg-emerald-500 rounded-full flex items-center justify-center text-white text-[10px] font-bold border-2 border-white">
                            0
                        </div>
                        <div className="w-12 h-0.5 bg-emerald-500/20 absolute bottom-4" />
                        <div className="w-8 h-0.5 bg-emerald-500/20 absolute bottom-6" />
                        <BarChart3 className="w-8 h-8 text-emerald-500/30" />
                    </div>
                </div>

                <h3 className="text-xl font-bold text-slate-800 mb-2">
                    View In-Depth Insights
                </h3>
                <p className="text-slate-400 text-sm max-w-sm text-center">
                    See the number of views, clicks and leads that your listing has received.
                </p>
            </div>
        </div>
    );
}