@echo off
echo ========================================
echo VisionCraft Backend API Test
echo ========================================
echo.

echo Testing Categories Endpoint...
curl -s http://localhost:8000/api/categories
echo.
echo.

echo Testing Prompts Endpoint...
curl -s http://localhost:8000/api/prompts
echo.
echo.

echo Testing Featured Prompts...
curl -s http://localhost:8000/api/prompts/featured
echo.
echo.

echo Testing Blogs Endpoint...
curl -s http://localhost:8000/api/blogs
echo.
echo.

echo ========================================
echo Test Complete!
echo ========================================
pause
