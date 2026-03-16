"use client";
import { useState, useEffect } from "react";

export const useDashboardStats = () => {
  const [data, setData] = useState({
    listings: {
      forSale: 0,
      forRent: 0,
      superHot: 0,
      hot: 0,
    },
    credits: {
      available: 0,
      used: 0,
      total: 0,
    },
    analytics: {
      views: 0,
      clicks: 0,
      leads: 0,
      calls: 0,
      whatsapp: 0,
      sms: 0,
      emails: 0,
    },
  });

  return { data };
};