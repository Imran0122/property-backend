// FILE: /app/api/properties/route.js
import { prisma } from "@/lib/prisma"; // ya jo DB client use kar rahe ho

export async function POST(req) {
  try {
    const data = await req.json();

    // Basic validation
    if (!data.title || !data.city) {
      return new Response(
        JSON.stringify({ status: false, message: "Title and City required" }),
        { status: 400 }
      );
    }

    // Save to DB (example with Prisma)
    const property = await prisma.property.create({
      data: {
        title: data.title,
        description: data.description,
        city: data.city,
        location: data.location,
        price: parseFloat(data.price) || 0,
        landArea: data.landArea || "",
        unit: data.unit || "Marla",
        bedrooms: data.bedrooms || 0,
        bathrooms: data.bathrooms || 0,
        purpose: data.purpose || "sell",
        propertyType: data.propertyType || "",
        email: data.email || "",
        phone: data.phone || "",
        landline: data.landline || "",
        images: data.images || [],
        status: "active",
      },
    });

    return new Response(
      JSON.stringify({ status: true, data: property }),
      { status: 200 }
    );
  } catch (error) {
    return new Response(
      JSON.stringify({ status: false, message: error.message }),
      { status: 500 }
    );
  }
}